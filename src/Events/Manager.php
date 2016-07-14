<?php
namespace Simox\Events;

class Manager
{
	private $attached_events;
	
	public function __construct()
	{
		$this->attached_events = array();
	}
    
    public function _explodeEventName( $event_name )
    {
        $target = explode( ":", $event_name );
        return array( "component_name" => $target[0], "task_name" => $target[1] );
    }
	
	public function attach( $event_name, $plugin )
	{
		$this->attached_events[$event_name] = $plugin;
	}
	
	public function fire( $event_name, $component )
	{
        if ( isset($this->attached_events[$event_name]) )
        {
            $target = $this->_explodeEventName( $event_name );
            if ( method_exists($this->attached_events[$event_name], $target["task_name"]) )
            {
                $this->attached_events[$event_name]->$target["task_name"]( $component );
            }
        }
	}
}
