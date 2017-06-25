<?php
namespace Simox\Router;

class Route
{
    private $_uri;

    private $_controllerName;

    private $_actionName;

    private $_params;

    public function __construct( $options = [] )
    {
        /**
         * Set the properties
         */
        $this->_uri            = isset($options["uri"]) ? $options["uri"] : null;

        $this->_controllerName = isset($options["controllerName"]) ? $options["controllerName"] : null;

        $this->_actionName     = isset($options["actionName"]) ? $options["actionName"] : null;

        $this->_params         = isset($options["params"]) ? $options["params"] : array();

        /**
         * Call the set functions for controllerName and actionName
         * to make sure they are formatted correctly
         */
        $this->setControllerName( $this->_controllerName );

        $this->setActionName( $this->_actionName );
    }

    public function setUri( $uri )
    {
        $this->_uri = $uri;
    }

    public function getUri()
    {
        return $this->_uri;
    }

    /**
     * Formats a controllerName correctly
     * iNDeXCOntrLleR -> indexController
     */
    public function setControllerName( $controllerName )
    {
        $controllerName = strtolower( $controllerName );

        $controllerName = str_replace("controller", "", $controllerName);

        $controllerName = ucfirst($controllerName) . "Controller";

        $this->_controllerName = $controllerName;
    }

    public function getControllerName()
    {
        return $this->_controllerName;
    }

    public function setActionName( $actionName )
    {
        $actionName = strtolower( $actionName );

        $actionName = str_replace("action", "", $actionName);

        $actionName = $actionName . "Action";

        $this->_actionName = $actionName;
    }

    public function getActionName()
    {
        return $this->_actionName;
    }

    public function setParams( $params )
    {
        $this->_params = isset($params) ? $params : array();
    }

    public function getParams()
    {
        return $this->_params;
    }
}
