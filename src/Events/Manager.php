<?php
namespace Simox\Events;

class Manager
{
	private $_events;
	
	public function __construct()
	{
		$this->_events = array();
	}
	
	public function attach( $event_name, $plugin_definition )
	{
		$this->_events[$event_name] = $plugin_definition;
	}
	
	public function fire( $event_name, $component, $params = null )
	{
        if ( isset($this->_events[$event_name]) )
        {
            $plugin_definition = $this->_events[$event_name];
            
            if ( $plugin_definition instanceof \Closure )
            {
                /**
                 * If the plugin definition is a closure -> run the definition
                 */
                $plugin_definition( $component, $params );

                return;
            }
            else if ( gettype($plugin_definition) == "object" )
            {
                /**
                 * If the definition we need to find the function name from the event name
                 */
                $event_name = explode( ":", $event_name );
                $component_name = $event_name[0];
                $event_function_name = $event_name[1];

                if ( method_exists($plugin_definition, $event_function_name) )
                {
                    /**
                     * Run the function name in the definition
                     */
                    $plugin_definition->$event_function_name( $component, $params );
                }

                return;
            }
        }
	}
}
