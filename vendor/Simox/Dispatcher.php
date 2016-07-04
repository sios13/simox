<?php
namespace Simox;

class Dispatcher extends SimoxServiceBase implements Events\EventsAwareInterface
{
    private $controller;
    private $action;
    private $params;
    
    private $controller_name;
    private $action_name;
	
	private $events_manager;
	
	private $was_forwarded;
    
    private $no_match_path;

    public function __construct()
    {
		$this->was_forwarded = false;
    }
    
	public function setEventsManager( Events\Manager $events_manager )
	{
		$this->events_manager = $events_manager;
	}
	
	public function getEventsManager()
	{
		return $this->events_manager;
	}
	
    /**
     * @return Returns the name of the controller being dispatched
     */
	public function getControllerName()
	{
        $this->controller_name = strtolower( $this->controller_name );
        $this->controller_name = str_replace( "controller", "", $this->controller_name );
        return $this->controller_name;
	}
	
    /**
     * @return Returns the name of the action being dispatched
     */
	public function getActionName()
	{
        $this->action_name = strtolower( $this->action_name );
        $this->action_name = str_replace( "action", "", $this->action_name );
        return $this->action_name;
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
		$controller = ucfirst( strtolower( $params["controller"] ) ) . "Controller";
		$action = strtolower( $params["action"] ) . "Action";
        
		$this->controller = new $controller();
		$this->action = $action;
        $this->params = $params["params"];
		
		$this->was_forwarded = true;
	}
    
    /**
     * Sets the $no_match_path
     *
     * If the router tells the dispatcher that there is no match,
     * the $no_match_path can tell the dispatcher what controller#action to call
     * 
     * @param array $target ["controller" => "index", "action" => "index"]
     */
    public function setNoMatchPath( $target )
    {
        $this->no_match_path = array( "target" => $target, "params" => array() );
    }
    
    /**
     * Private helper function to explode a target (Controller#action) into array
     * 
     * @param string $target
     * @return array
     */
    private function _explodeTarget( $target )
    {
        $target = explode( "#", $target );
        
        return array(
            "controller_name" => $target[0],
            "action_name" => $target[1]
        );
    }
    
    /**
     * Dispatch the flow to a controller/action provided by the router
     */
    public function dispatch( $match )
    {
        // If there was no match
        if ( $match == false )
        {
            // 404 header
            header("HTTP/1.0 404 Not Found");
            
            if ( isset($this->no_match_path) )
            {
                $match = $this->no_match_path;
            }
            else
            {
                throw new \Exception("404. Page not found.");
            }
        }
        
        // Controller#action
        $target = $this->_explodeTarget( $match["target"] );
        
        $this->controller_name = $target["controller_name"];
        $this->action_name = $target["action_name"];
        
        $this->controller = new $target["controller_name"]();
        $this->action = $target["action_name"];
        $this->params = $match["params"];
        
        
		// Calling before dispatch listener
		if ($this->events_manager) {
			$this->events_manager->fire( "dispatch:beforeDispatch", $this );
			
			// If there was a forward in the listener
			if ($this->was_forwarded == true) {
				$this->was_forwarded = false;
				//continue;
			}
		}
		
		$finished = false;
		
		$number_dispatches = 0;
		
		while (!$finished)
		{
			$number_dispatches++;
			
			if ($number_dispatches == 100) {
				throw new \Exception("Dispatch error. Cyclic routing.");
				break;
			}
			
			// Calling action in controller with params
            $this->controller->initialize();
			call_user_func_array( array($this->controller, $this->action), $this->params );
			
			// If there was a forward in the action
			if ($this->was_forwarded == true) {
				$this->was_forwarded = false;
				continue;
			}
			
			$finished = true;
		}
    }
}
