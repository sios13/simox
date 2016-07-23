<?php
namespace Simox\Router;

class Route
{
    private $_controller_name;
    
    private $_action_name;
    
    private $_params;
    
    private $_uri;
    
    public function __construct( $uri = null )
    {
        $this->_uri = isset($uri) ? $this->_format( $uri ) : null;
    }
    
    public function getUri()
    {
        return $this->_uri;
    }
    
    public function getControllerName()
    {
        return $this->_controller_name;
    }
    
    public function getActionName()
    {
        return $this->_action_name;
    }
    
    public function getParams()
    {
        return $this->_params;
    }
    
    public function setControllerName( $controller_name )
    {
        $controller_name = strtolower( $controller_name );
        
        $controller_name = str_replace("controller", "", $controller_name);
        
        $controller_name = ucfirst($controller_name) . "Controller";
        
        $this->_controller_name = $controller_name;
    }
    
    public function setActionName( $action_name )
    {
        $action_name = strtolower( $action_name );
        
        $action_name = str_replace("action", "", $action_name);
        
        $action_name = $action_name . "Action";
        
        $this->_action_name = $action_name;
    }
    
    public function setParams( $params )
    {
        $this->_params = $params;
    }
    
    /**
     * Helper function to format route uris and request uris to the same consistent format
     */
    private function _format( $target )
    {
        /**
         * If there is no prepending slash, add it
         */
        if ( $target[0] != "/" )
        {
            $target = "/" . $target;
        }
        
        /**
         * If there is no appending slash, add it
         */
        if ( $target[strlen($target)-1] != "/" )
        {
            $target = $target . "/";
        }
        
        return $target;
    }
}
