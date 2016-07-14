<?php
namespace Simox;

class Dispatcher extends SimoxServiceBase implements Events\EventsAwareInterface
{
    private $_controller_name;
    private $_action_name;
    private $_params;
	
	private $_events_manager;
	
	private $_was_forwarded;
    
    const EXCEPTION_CYCLIC_ROUTING = 0;
    
    const EXCEPTION_CONTROLLER_NOT_FOUND = 1;
    
    const EXCEPTION_ACTION_NOT_FOUND = 2;

    public function __construct()
    {
        $this->_params = array();
        
		$this->was_forwarded = false;
    }
    
	public function setEventsManager( Events\Manager $events_manager )
	{
		$this->_events_manager = $events_manager;
	}
	
	public function getEventsManager()
	{
		return $this->_events_manager;
	}
    
    public function setControllerName( $controller_name )
    {
        $this->_controller_name = $controller_name;
    }
    
    public function setActionName( $action_name )
    {
        $this->_action_name = $action_name;
    }
    
    public function setParams( $params )
    {
        $this->_params = $params;
    }
	
    /**
     * @return Returns the name of the controller being dispatched
     */
	public function getControllerName()
	{
        return $this->_controller_name;
	}
	
    /**
     * @return Returns the name of the action being dispatched
     */
	public function getActionName()
	{
        return $this->_action_name;
	}
    
    /**
     * Handles the exceptions thrown in the Dispatcher.
     * The exception can be handled by the "beforeException" listener.
     * If the listener returns false, _throwDispatchException also returns false.
     * The header status code is always set to 404.
     */
    private function _throwDispatchException( $message, $exception_code = 0 )
    {
        /**
         * Sets a 404 header
         */
        $this->response->setStatusCode( 404, "Not found" );
        
        $exception = new \Exception( $message, $exception_code );
        
		if ( is_object($this->_events_manager) )
        {
            /**
             * If the event returns false, exit early and don't throw exception
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
        $this->_controller_name = $params["controller"];
        $this->_action_name     = $params["action"];
        $this->_params          = isset($params["params"]) ? $params["params"] : array();
        
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
			
			if ($number_dispatches == 100)
            {
                $this->_throwDispatchException( "Dispatch error. Cyclic routing.", self::EXCEPTION_CYCLIC_ROUTING );
				break;
			}
            
            $controller_class = ucfirst($this->_controller_name) . "Controller";
            
            if ( !class_exists($controller_class) )
            {
                if ( $this->_throwDispatchException( $this->_controller_name . " controller class cannot be loaded.", self::EXCEPTION_CONTROLLER_NOT_FOUND ) == false )
                {
                    continue;
                }
                break;
            }
            
            $controller = new $controller_class();
            
            if ( !method_exists($controller, $this->_action_name . "Action") )
            {
                if ( $this->_throwDispatchException( $this->_action_name . "Action action does not exist in " . $controller_class . ".", self::EXCEPTION_ACTION_NOT_FOUND ) == false )
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
            
            $controller->initialize();
            
			/**
             * Calling action in controller with params
             */
			call_user_func_array( array($controller, $this->_action_name . "Action"), $this->_params );
			
			/**
             * If there was a forward in the action
             */
			if ($this->_was_forwarded == true)
            {
				$this->_was_forwarded = false;
				continue;
			}
			
			$finished = true;
		}
    }
}
