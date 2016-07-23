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
    private function createRoute( $uri, $controller_action = null )
    {
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
        $this->_not_found_route = $this->createRoute( $uri, $controller_action );
    }
    
    /**
     * Adds a route.
     * 
     * @param string $uri
     * @param string $target the target controller#action
     */
    public function addRoute( $uri, $controller_action )
    {
        $this->_routes[] = $this->createRoute( $uri, $controller_action );
    }
    
    /**
     * Match the requested uri with the route uris
     */
    public function handle( $request_uri )
    {
        /**
         * Remove the base uri from the request uri
         */
        $request_uri = str_replace( $this->url->getBaseUri(), "", $request_uri );
        
        /**
         * If the base uri replaces the entire request uri
         * (If base uri equals request uri)
         */
        if ( $request_uri == null )
        {
            $request_uri = "/";
        }
        
        $request_route = new Route( $request_uri );
        
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