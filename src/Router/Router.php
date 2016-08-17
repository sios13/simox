<?php
namespace Simox;

use Simox\DI\DIAwareInterface;
use Simox\Router\Route;

class Router implements DIAwareInterface
{
    private $_di;

    private $_routes;
    
    private $_route;
    
    private $_not_found_route;
    
    public function __construct()
    {
        $this->_routes = array();
        
        $this->_route = new Route();
    }

    public function setDI( $di )
    {
        $this->_di = $di;
    }

    public function getDI()
    {
        return $this->_di;
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
        $url = $this->_di->getService( "url" );

        $uri_prefix = $url->getUriPrefix();

        /**
         * If uri prefix is set
         */
        if ( isset($uri_prefix) )
        {
            /**
             * Remove the uri prefix from the request uri
             */
            $request_uri = str_replace( $uri_prefix, "", $request_uri );

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

        /**
         * Passing the uri to the get function in the url service to make sure the uri:s have a consistent format
         */
        $request_route->setUri( $url->get( $request_route->getUri() ) );

        $request_route_uri_fragments = explode( "/", $request_route->getUri() );
        
        foreach ($this->_routes as $route)
        {
            /**
             * Passing the uri to the get function in the url service to make sure the uri:s have a consistent format
             */
            $route->setUri( $url->get( $route->getUri() ) );

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
             * Compare every fragment of the uris
             */
            for ($i = 0; $i < count($route_uri_fragments); $i++)
            {
                /**
                 * Parameters are not compared
                 */
                if ( $route_uri_fragments[$i] == "{param}" )
                {
                    $params[] = $request_route_uri_fragments[$i];
                    continue;
                }
                
                /**
                 * If a fragment is not equal -> continue to next route
                 */
                if ( $route_uri_fragments[$i] != $request_route_uri_fragments[$i] )
                {
                    continue 2;
                }
            }
            
            /**
             * We have a match!
             */
            $this->_route->setControllerName( $route->getControllerName() );
            $this->_route->setActionName( $route->getActionName() );
            $this->_route->setParams( $params );
            
            return;
        }
        
        if ( isset($this->_not_found_route) )
        {
            $this->_route = $this->_not_found_route;
        }
    }
}
