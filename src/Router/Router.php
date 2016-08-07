<?php
namespace Simox;

use Simox\Router\Route;

class Router extends SimoxServiceBase
{
    private $_routes;
    
    private $_route;
    
    private $_not_found_route;
    
    public function __construct()
    {
        $this->_routes = array();
        
        $this->_route = new Route();
    }
    
    public function getRoute()
    {
        return $this->_route;
    }
    
    /**
     * Helper function to create a route
     */
    private function _createRoute( $uri, $controller_action = null )
    {
        /**
         * Passing the uri to the url service to make sure the uri:s have a consistent format
         */
        $uri = $this->url->get( $uri . "/" );

        $route = new Route( $uri );
        
        if ( isset($controller_action) )
        {
            if ( is_array($controller_action) )
            {
                $route->setControllerName( $controller_action["controller"] );
                $route->setActionName( $controller_action["action"] );
            }
            else
            {
                $controller_action = explode( "#", $controller_action );
                
                $route->setControllerName( $controller_action[0] );
                $route->setActionName( $controller_action[1] );
            }
        }
        
        return $route;
    }
    
    public function notFoundRoute( $uri, $controller_action )
    {
        $this->_not_found_route = $this->_createRoute( $uri, $controller_action );
    }
    
    /**
     * Adds a route.
     * 
     * @param string $uri
     * @param string $target the target controller#action
     */
    public function addRoute( $uri, $controller_action )
    {
        $this->_routes[] = $this->_createRoute( $uri, $controller_action );
    }
    
    /**
     * Returns a route uri given controller name and action name
     */
    public function reverseRoute( $controller_name, $action_name )
    {
        $_route = $this->_createRoute( null, array("controller" => $controller_name, "action" => $action_name) );
        
        foreach ( $this->_routes as $route )
        {
            if ( $_route->getControllerName() == $route->getControllerName() && $_route->getActionName() == $route->getActionName() )
            {
                return $route->getUri();
            }
        }
    }
    
    /**
     * Match a request uri with the route uris
     */
    public function handle( $request_uri )
    {
        /**
         * If uri prefix is set
         */
        if ( $this->url->getUriPrefix() != null )
        {
            /**
             * Remove the uri prefix from the request uri
             */
            $request_uri = str_replace( $this->url->getUriPrefix(), "", $request_uri );

            /**
             * If the uri prefix replaces the entire request uri
             * (If uri prefix equals request uri)
             */
            if ( $request_uri == null )
            {
                $request_uri = "/";
            }
        }
        
        $request_route = $this->_createRoute( $request_uri );
        
        $request_route_uri_fragments = explode( "/", $request_route->getUri() );
        
        foreach ($this->_routes as $route)
        {
            $route_uri_fragments = explode( "/", $route->getUri() );
            
            /**
             * Check if request uri and route uri has the same number of fragments. 
             * If not, continue to next route.
             */
            if ( count($route_uri_fragments) != count($request_route_uri_fragments) )
            {
                continue;
            }
            
            $params = array();
            
            /**
             * Checks if every fragment of the uris are the same
             */
            for ($i = 0; $i < count($route_uri_fragments); $i++)
            {
                if ( $route_uri_fragments[$i] ==  "{param}" )
                {
                    $params[] = $request_route_uri_fragments[$i];
                    continue;
                }
                
                if ( $route_uri_fragments[$i] != $request_route_uri_fragments[$i] )
                {
                    continue 2;
                }
            }
            
            $request_route->setControllerName( $route->getControllerName() );
            $request_route->setActionName( $route->getActionName() );
            $request_route->setParams( $params );
            
            $this->_route = $request_route;
            
            return;
        }
        
        if ( isset($this->_not_found_route) )
        {
            $this->_route = $this->_not_found_route;
        }
    }
}
