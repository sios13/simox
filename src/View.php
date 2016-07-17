<?php
namespace Simox;

class View extends SimoxServiceBase
{
    private $_content;
    
    private $_views_dir;
    
    private $_view_levels;
    
    private $_output_callable;
    
    private $_cache_service_name;
    
    private $_render_completed;
    
    public function __construct()
	{
        $this->_views_dir = $this->url->getBaseUri() . "/app/views/";
        
        $this->_render_completed = false;
        
        $this->_view_levels = array(
            "MAIN_VIEW" => array(
                "name" => "MAIN_VIEW",
                "level" => 5,
                "file_name" => null,
                "cache_enabled" => false,
                "is_disabled" => true
            ),
            "BEFORE_CONTROLLER_VIEW" => array(
                "name" => "BEFORE_CONTROLLER_VIEW",
                "level" => 4,
                "file_name" => null,
                "cache_enabled" => false,
                "is_disabled" => true
            ),
            "CONTROLLER_VIEW" => array(
                "name" => "CONTROLLER_VIEW",
                "level" => 3,
                "file_name" => null,
                "cache_enabled" => false,
                "is_disabled" => true
            ),
            "AFTER_CONTROLLER_VIEW" => array(
                "name" => "AFTER_CONTROLLER_VIEW",
                "level" => 2,
                "file_name" => null,
                "cache_enabled" => false,
                "is_disabled" => true
            ),
            "ACTION_VIEW" => array(
                "name" => "ACTION_VIEW",
                "level" => 1,
                "file_name" => null,
                "cache_enabled" => false,
                "is_disabled" => true
            ),
        );
	}
    
    public function __set( $name, $value )
    {
        $this->setVar( $name, $value );
    }
    
    public function setVar ( $name, $value )
    {
        $this->$name = $value;
    }
    
    public function setViewsDir( $views_dir )
    {
        $this->_views_dir = $views_dir;
    }
    
    /**
     * Attach a file name to a view level, enables the view level
     */
    public function setViewLevel( $view_level_name, $view_level_file_name )
    {
        if ( isset($this->_view_levels[$view_level_name]) )
        {
            $this->_view_levels[$view_level_name]["file_name"] = $view_level_file_name;
            $this->_view_levels[$view_level_name]["is_disabled"] = false;
        }
    }
    
    /**
     * Sets the action view
     */
    public function pick( $view_level_file_name )
    {
        $this->setViewLevel( "ACTION_VIEW", $view_level_file_name );
    }
    
    /**
     * Sets the main view
     */
    public function setMainView( $view_level_file_name )
    {
        $this->setViewLevel( "MAIN_VIEW", $view_level_file_name );
    }
    
    /**
     * Disables a view level
     */
    public function disableViewLevel( $view_level_name )
    {
        if ( isset($this->_view_levels[$view_level_name]) )
        {
            $this->_view_levels[$level_name]["is_disabled"] = true;
        }
    }
    
    /**
     * Set a callable to filter output
     */
    public function setOutputCallable( $callable )
    {
        $this->_output_callable = $callable;
    }
    
    /**
     * Enables caching.
     */
    public function enableCache ( $options = null )
    {
        $key = isset($options["key"]) ? $options["key"] : "";
        $lifetime = isset($options["lifetime"]) ? $options["lifetime"] : 3600;
        $level = isset($options["level"]) ? $options["level"] : 1;
        $this->_cache_service_name = isset($options["service"]) ? $options["service"] : "cache";
        
        $view_level_name = $this->_getViewLevelNameFromLevel( $level );
        
        $this->_view_levels[$view_level_name]["cache_enabled"] = array(
            "key" => $key,
            "lifetime" => $lifetime
        );
    }
    
    public function render()
    {
        ob_start();
        
        // Output enabled view levels
        $this->getContent();
        
        $content = ob_get_contents();
        
        ob_end_clean();
        
        if ( isset($this->_output_callable) )
        {
            $content = call_user_func( $this->_output_callable, $content );
        }
        
       $this->_content = $content;
       
       $this->_render_completed = true;
    }
    
    /**
     * Includes the files attached to the view levels.
     * Disabled view levels do not get included.
     * MAIN_VIEW -> BEFORE_CONTROLLER_VIEW -> CONTROLLER_VIEW -> AFTER_CONTROLLER_VIEW -> ACTION_VIEW
     * If a view level has cache enabled, stop rendering
     */
    public function getContent()
    {
        if ( $this->_render_completed )
        {
            return $this->_content;
        }
        
        foreach ( $this->_view_levels as $view_level )
        {
            if ( $view_level["is_disabled"] )
            {
                continue;
            }
            
            $this->_view_levels[$view_level["name"]]["is_disabled"] = true;
            
            if ( $view_level["cache_enabled"] != false )
            {
                $this->getCacheContent( $view_level );
                return;
            }
            
            include( $this->_views_dir . $view_level["file_name"] . ".phtml" );
            return;
        }
    }
    
    public function getCacheContent( $view_level )
    {
        /**
         * If the cache does not exist, create the cache
         */
        $cache = $this->_cache_service_name;
        
        if ( !$this->$cache->exists( $view_level["cache_enabled"]["key"], $view_level["cache_enabled"]["lifetime"] ) )
        {
            $this->$cache->start( $view_level["cache_enabled"]["key"] );
            
            include( $this->_views_dir . $view_level["file_name"] . ".phtml" );
            
            $this->$cache->save();
            
        }
        
        $this->$cache->get( $view_level["cache_enabled"]["key"] );
    }
    
    /**
     * Returns a view level name (string) given a level (integer)
     */
    private function _getViewLevelNameFromLevel( $view_level_level )
    {
        foreach ( $this->_view_levels as $view_level )
        {
            if ( $view_level["level"] == $view_level_level )
            {
                return $view_level["name"];
            }
        }
    }
    
    /**
     * Returns a level (integer) given a view level name (string)
     */
    private function _getViewLevelFromViewLevelName( $view_level_name )
    {
        foreach ( $this->_view_levels as $view_level )
        {
            if ( $view_level["name"] == $view_level_name )
            {
                return $view_level["level"];
            }
        }
    }
}
