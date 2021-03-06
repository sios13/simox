<?php
namespace Simox;

use Simox\DI\DIAwareInterface;
use Simox\Events\EventsAwareInterface;
use Simox\Events\Manager as EventsManager;
use Simox\Router\Route;

class Dispatcher implements DIAwareInterface, EventsAwareInterface
{
    private $_di;

	private $_events_manager;

	private $_was_forwarded;

    private $_route;

    const EXCEPTION_CYCLIC_ROUTING = 0;

    const EXCEPTION_CONTROLLER_NOT_FOUND = 1;

    const EXCEPTION_ACTION_NOT_FOUND = 2;

    public function __construct()
    {
        $this->_di = null;

        $this->_events_manager = null;

		$this->_was_forwarded = false;

        $this->_route = new Route();
    }

    public function setDI( $di )
    {
        $this->_di = $di;
    }

    public function getDI()
    {
        return $this->_di;
    }

	public function setEventsManager( EventsManager $events_manager )
	{
		$this->_events_manager = $events_manager;
	}

	public function getEventsManager()
	{
		return $this->_events_manager;
	}

    public function setRoute( $route )
    {
        $this->_route = $route;
    }

    /**
     * @return Returns the name of the controller being dispatched
     */
	public function getControllerName()
	{
        return $this->_route->getControllerName();
	}

    /**
     * @return Returns the name of the action being dispatched
     */
	public function getActionName()
	{
        return $this->_route->getActionName();
	}

    /**
     * Handles the exceptions thrown in the Dispatcher.
     * The exception can be handled by the "beforeException" listener.
     * If the listener returns false, _throwDispatchException also returns false.
     * The header status code is always set to 404.
     */
    private function _throwDispatchException( $message, $exception_code = 0 )
    {
        $exception = new \Exception( $message, $exception_code );

		if ( is_object($this->_events_manager) )
        {
            /**
             * If the event returns false -> exit and don't throw exception
             */
            if ( $this->_events_manager->fire( "dispatch:beforeException", $this, array("exception" => $exception) ) == false )
            {
                return false;
            }
		}

        throw $exception;
    }

    /**
     * Tells the dispatcher to forward to another controller and action
     *
     * @param string $params["controller"] name of the controller
     * @param string $params["action"] name of the action
     * @param array $params["params"] parameters to the action
     */
	public function forward( $params )
	{
        $controller_name = $params["controller"];
        $action_name     = $params["action"];
        $params          = isset($params["params"]) ? $params["params"] : array();

        $this->_route->setControllerName( $controller_name );
        $this->_route->setActionName( $action_name );
        $this->_route->setParams( $params );

		$this->_was_forwarded = true;
	}

    /**
     * Dispatch to a controller/action provided by the router
     */
    public function dispatch()
    {
		/**
         * Calling before dispatch listener
         */
		if ( is_object($this->_events_manager) )
        {
            $this->_events_manager->fire( "dispatch:beforeDispatch", $this );
		}

		$finished = false;

		$number_dispatches = 0;

		while ( !$finished )
		{
			$number_dispatches++;

			if ( $number_dispatches == 100 )
            {
                $this->_throwDispatchException( "Dispatch error. Cyclic routing.", self::EXCEPTION_CYCLIC_ROUTING );
				break;
			}

            $controller_name = $this->_route->getControllerName();
            $action_name     = $this->_route->getActionName();
            $params          = $this->_route->getParams();

            if ( !class_exists($controller_name) )
            {
                if ( $this->_throwDispatchException( $controller_name . " controller class cannot be loaded.", self::EXCEPTION_CONTROLLER_NOT_FOUND ) == false )
                {
                    continue;
                }

                break;
            }

            $controller_instance = new $controller_name();

            if ( !method_exists($controller_instance, $action_name) )
            {
                if ( $this->_throwDispatchException( $action_name . "Action action does not exist in " . $controller_name . ".", self::EXCEPTION_ACTION_NOT_FOUND ) == false )
                {
                    continue;
                }

                break;
            }

            /**
             * Calling before execute route listener
             */
            if ( is_object($this->_events_manager) )
            {
                $this->_events_manager->fire( "dispatch:beforeExecuteRoute", $this );

                /**
                 * If there was a forward in the listener
                 */
                if ( $this->_was_forwarded )
                {
                    $this->_was_forwarded = false;
                    continue;
                }
            }

            $controller_instance->setDI( $this->_di );

            $controller_instance->initialize();

			/**
             * Calling action in controller with params
             */
            call_user_func_array( array($controller_instance, $action_name), $params );

			/**
             * If there was a forward in the action
             */
			if ( $this->_was_forwarded )
            {
				$this->_was_forwarded = false;
				continue;
			}

			$finished = true;
		}
    }
}
