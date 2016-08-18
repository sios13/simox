<?php
namespace Simox;

use Simox\DI\DIAwareInterface;
use Simox\Events\EventsAwareInterface;

class View implements DIAwareInterface, EventsAwareInterface
{
    private $_di;

    private $_events_manager;

    private $_content;
    
    private $_views_dir;
    
    private $_cache_service_name;
    
    private $_render_completed;
    
    private $_view_levels;
    
    public function __construct()
	{
        $this->_di = null;

        $this->_events_manager = null;

        $this->_content = null;

        $this->_views_dir = "/app/views/";

        $this->_cache_service_name = null;
        
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

    public function setDI( $di )
    {
        $this->_di = $di;
    }

    public function getDI()
    {
        return $this->_di;
    }
    
    public function setEventsManager( Events\Manager $events_manager )
    {
        $this->_events_manager = $events_manager;
    }
    
    public function getEventsManager()
    {
        return $this->_events_manager;
    }
    
    public function __set( $name, $value )
    {
        $this->setVar( $name, $value );
    }

    public function __get( $var_name )
    {
        if ( $this->_di->hasService($var_name) )
        {
            return $this->_di->getService( $var_name );
        }

        if ( $var_name == "di" )
        {
            return $this->_di;
        }
    }
    
    public function setVar ( $name, $value )
    {
        $this->$name = $value;
    }
    
    /**
     * Set the views dir relative to the public folder
     */
    public function setViewsDir( $views_dir )
    {
        $this->_views_dir = $this->url->get( $views_dir );
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
        $url = $this->_di->getService( "url" );

        /**
         * Prefix the views dir with the root path from the url service
         */
        $this->_views_dir = $url->getRootPath() . $this->_views_dir;

        $this->_checkViewsExist();

        /**
         * Start rendering
         */
        ob_start();
        
        $this->getContent();
        
        $render_output = ob_get_contents();
        
        /**
         * Stop rendering
         */
        ob_end_clean();

        /**
         * The output from the render is set as content
         */
        $this->setContent( $render_output );
       
        $this->_render_completed = true;

        /**
         * Calling after render listener
         */
        if ( is_object($this->_events_manager) )
        {
            $this->_events_manager->fire( "dispatch:afterRender", $this );
        }
    }

    /**
     * Private function. Checks the enabled view levels and makes sure the attached view exists.
     */
    private function _checkViewsExist()
    {
        foreach ( $this->_view_levels as $view_level )
        {
            if ( !$view_level["is_disabled"] )
            {
                if ( !file_exists($this->_views_dir . $view_level["file_name"] . ".phtml") )
                {
                    throw new \Exception( "View '". $view_level["file_name"] .".phtml' does not exist." );
                }
            }
        }
    }

    public function setContent( $content )
    {
        $this->_content = $content;
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
        $cache = $this->_cache_service_name;
        
        /**
         * If the cache does not exist, create the cache
         */
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
