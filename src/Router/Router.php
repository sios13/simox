<?php
namespace Simox;

use Simox\DI\DIAwareInterface;
use Simox\Router\Route;

class Router implements DIAwareInterface
{
    private $_di;

    private $_routeDefinitions;

    private $_matchRoute;

    private $_notFoundRoute;

    public function __construct()
    {
        $this->_di = null;

        $this->_routes = array();

        $this->_matchRoute = null;

        $this->_notFoundRoute = null;
    }

    public function setDI( $di )
    {
        $this->_di = $di;
    }

    public function getDI()
    {
        return $this->_di;
    }

    /**
     * Returns the match route
     */
    public function getMatchRoute()
    {
        return $this->_matchRoute;
    }

    /**
     * Helper function to create a route
     */
    private function _createRoute( $options )
    {
        $url = $this->_di->getService( "url" );

        /**
         * All route uris should have the uri prefix
         */
        $options["uri"] = $url->getUriPrefix() . $options["uri"];

        if (isset($options["controller_action"]))
        {
            $controller_action_exploded = explode( "#", $options["controller_action"] );

            $options["controllerName"] = $controller_action_exploded[0];
            $options["actionName"]     = $controller_action_exploded[1];
        }

        // $uri            = $settings["uri"];
        //
        // $controllerName = $settings["controllerName"];
        //
        // $actionName     = $settings["actionName"];
        //
        // $params         = $settigns["params"];

        return new Route($options);
    }

    public function notFoundRoute( $uri, $controller_action )
    {
        $this->_notFoundRoute = $this->_createRoute( $uri, $controller_action );
    }

    /**
     * Adds a route.
     *
     * @param string $uri
     * @param string $target the target controller#action
     */
    public function addRoute( $uri, $controller_action )
    {
        $routeOptions = [];
        $routeOptions["uri"] = $uri;
        $routeOptions["controller_action"] = $controller_action;

        $routeDefinition = function() use ($routeOptions) {
            return $this->_createRoute( $routeOptions );
        };

        $this->_routeDefinitions[] = $routeDefinition;
    }

    /**
     * Returns a route uri given controller name and action name
     */
    public function reverseRoute( $controllerName, $actionName )
    {
        // Create a temp route because formatting
        $tempRoute = new Route( ["controllerName" => $controllerName, "actionName" => $actionName] );

        foreach ( $this->_routeDefinitions as $routeDefinition )
        {
            $route = call_user_func($routeDefinition);

            if ( $tempRoute->getControllerName() == $route->getControllerName() && $tempRoute->getActionName() == $route->getActionName() )
            {
                return $route->getUri();
            }
        }
    }

    /**
     * Match a request uri with the route uris registered in this service
     */
    public function handle( $requestUri )
    {
        $url = $this->_di->getService( "url" );

        /**
         * Remove the uri prefix from the request uri
         * (the uri prefix is added when route is created)
         */
        $requestUri = str_replace( $url->getUriPrefix(), "", $requestUri );

        $request_route = $this->_createRoute( ["uri" => $requestUri] );

        /**
         * Passing the uri to the get function in the url service to make sure the uri:s have a consistent format
         */
        //$request_route->setUri( $url->get( $request_route->getUri() ) );

        $request_route_uri_fragments = explode( "/", $request_route->getUri() );

        foreach ($this->_routeDefinitions as $routeDefinition)
        {
            $route = call_user_func($routeDefinition);

            /**
             * Passing the uri to the get function in the url service to make sure the uri:s have a consistent format
             */
            //$route->setUri( $url->get( $route->getUri() ) );

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
            for ( $i = 0; $i < count($route_uri_fragments); $i++ )
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

            $route->setParams( $params );

            /**
             * We have a match!
             */
            $this->_matchRoute = $route;

            return;
        }

        /**
         * No match :(
         * If not found route has been set -> set as out match route
         */
        if ( isset($this->_notFoundRoute) )
        {
            $this->_matchRoute = $this->_notFoundRoute;

            return;
        }

        /**
         * With no other options, we set the match route to a default route
         */
        $this->_matchRoute = new Route();
    }
}
