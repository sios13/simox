<?php
namespace Simox;

class Router extends SimoxServiceBase
{
    private $_routes;
    
    private $_controller_name;
    private $_action_name;
    private $_params;
    
    public function __construct()
    {
        $this->_routes = array();
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
    
    /**
     * Adds a route.
     * Routes are compared to the requested route.
     * 
     * @param string $path
     * @param string $target the target controller#action
     */
    public function addRoute( $path, $target )
    {
        if ( is_array($target) )
        {
            $controller_name = $target["controller"];
            $action_name = $target["action"];
        }
        else
        {
            $target = explode( "#", $target );
            $controller_name = str_replace( "Controller", "", $target[0] );
            $action_name = str_replace( "Action", "", $target[1] );
        }
        
        $this->_routes[] = array(
            "path" => $path,
            "controller_name" => strtolower($controller_name),
            "action_name" => strtolower($action_name)
        );
    }
    
    /**
     * Match the requested url with the routes
     * If match, returns route information
     * If no match, returns false
     * 
     * @return
     */
    public function handle()
    {
        /**
         * Get requested url
         */
        $request_url = $this->request->getServer("REQUEST_URI");
        
        /**
         * Remove base uri from request url
         */
        $request_url = str_replace( $this->url->getBaseUri(), "", $request_url );
        
        /**
         * If there is no prepending slash, add it
         */
        if ( $request_url != "" && $request_url[0] !== "/" )
        {
            $request_url = "/" . $request_url;
        }
        
        /**
         * If there is no appending slash, add it
         */
        if ($request_url[strlen($request_url)-1] !== "/")
        {
            $request_url = $request_url . "/";
        }
        
        /**
         * Get request method
         */
        $request_method = $this->request->getServer( "REQUEST_METHOD" );

        foreach ($this->_routes as $route)
        {
            /*
            // Check if request method matches. If not, continue to next route
            if ($route["method"] !== $request_method)
            {
                continue;
            }
            */
            
            /**
             * Add slash to request url if it does not exists
             */
            if ($route["path"][0] !== "/")
            {
                $route["path"] = "/" . $route["path"];
            }
            
            /**
             * Add trailing slash to route url if it does not exists
             */
            if ($route["path"][strlen($route["path"])-1] !== "/")
            {
                $route["path"] = $route["path"] . "/";
            }
            
            $exploded_route_url = explode( "/", $route["path"] );
            $exploded_request_url = explode( "/", $request_url );
            
            /**
             * Check if request and route url has the same number of parts. If not, continue to next route
             */
            if ( count($exploded_route_url) != count($exploded_request_url) )
            {
                continue;
            }
            
            $params = array();
            
            /**
             * Checks if every part of the url:s are the same
             */
            for ($i = 0; $i < count($exploded_route_url); $i++)
            {
                if ( $exploded_route_url[$i] ==  "{param}" )
                {
                    $params[] = $exploded_request_url[$i];
                    continue;
                }
                
                if ( $exploded_route_url[$i] != $exploded_request_url[$i] )
                {
                    continue 2;
                }
            }
            
            $this->_controller_name = $route["controller_name"];
            $this->_action_name = $route["action_name"];
            $this->_params = $params;
        }
    }
}
