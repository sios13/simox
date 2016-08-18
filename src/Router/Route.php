<?php
namespace Simox\Router;

class Route
{
    private $_uri;

    private $_controller_name;
    
    private $_action_name;
    
    private $_params;
    
    public function __construct( $uri = null )
    {
        $this->_uri = $uri;

        $this->_controller_name = null;

        $this->_action_name = null;

        $this->_params = array();
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

    public function setUri( $uri )
    {
        $this->_uri = $uri;
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
        $this->_params = isset($params) ? $params : array();
    }
}
