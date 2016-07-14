<?php
namespace Simox\Events;

class Manager
{
	private $_events;
	
	public function __construct()
	{
		$this->_events = array();
	}
    
    public function _explodeEventName( $event_name )
    {
        $target = explode( ":", $event_name );
        
        return array( "component_name" => $target[0], "task_name" => $target[1] );
    }
	
	public function attach( $event_name, $plugin )
	{
		$this->_events[$event_name] = $plugin;
	}
	
	public function fire( $event_name, $component, $params = null )
	{
        if ( isset($this->_events[$event_name]) )
        {
            $target = $this->_explodeEventName( $event_name );
            
            if ( method_exists($this->_events[$event_name], $target["task_name"]) )
            {
                return $this->_events[$event_name]->$target["task_name"]($component, $params);
            }
            else if ( is_callable($this->_events[$event_name]) )
            {
                return call_user_func_array( $this->_events[$event_name], array($component, $params) );
            }
        }
	}
}
