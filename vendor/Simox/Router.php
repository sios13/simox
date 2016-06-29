<?php
namespace Simox;

class Router extends SimoxServiceBase
{
    private $routes;
    
    public function __construct()
    {
        $this->routes = array();
    }
    
    public function addRoute($path, $target)
    {
        $this->routes[] = array(
            //"method" => $method,
            "path" => $path,
            "target" => $target
        );
    }
    
    // Returns a route path given a controller and action (target)
    public function reverseRoute( $controller, $action )
    {
        $controller_action_name = ucfirst(strtolower($controller)) . "Controller" . "#" . strtolower($action) . "Action";
        
        foreach ( $this->routes as $route )
        {
            if ( $controller_action_name == $route["target"] )
            {
                return $route["path"];
            }
        }
        
        return false;
    }
    
    // Match the request url with routes
    // If match, returns route information
    // If no match, returns false
    public function match()
    {
        // Get requested url
        $requestUrl = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "/";
        
        // Remove base uri from request url
        $requestUrl = substr( $requestUrl, strlen($this->url->getBaseUri()) );
        
        // Add slash to request url if it does not exists
        if ($requestUrl[0] !== "/")
        {
            $requestUrl = "/" . $requestUrl;
        }
        
        // Add trailing slash to request url if it does not exists
        if ($requestUrl[strlen($requestUrl)-1] !== "/")
        {
            $requestUrl = $requestUrl . "/";
        }
        
        // Get request method
        $requestMethod = isset($_SERVER["REQUEST_METHOD"]) ? $_SERVER["REQUEST_METHOD"] : "GET";

        foreach ($this->routes as $route)
        {
            /*
            // Check if request method matches. If not, continue to next route
            if ($route["method"] !== $requestMethod)
            {
                continue;
            }
            */
            // Add slash to request url if it does not exists
            if ($route["path"][0] !== "/")
            {
                $route["path"] = "/" . $route["path"];
            }
            
            // Add trailing slash to route url if it does not exists
            if ($route["path"][strlen($route["path"])-1] !== "/")
            {
                $route["path"] = $route["path"] . "/";
            }
            
            $explodedRouteUrl = explode( "/", $route["path"] );
            $explodedRequestUrl = explode( "/", $requestUrl );
            
            // Check if request and route url has the same number of parts. If not, continue to next route
            if ( count($explodedRouteUrl) != count($explodedRequestUrl) )
            {
                continue;
            }
            
            $params = array();
            
            // Checks if every part of the url:s are the same
            for ($i = 0; $i < count($explodedRouteUrl); $i++)
            {
                if ( $explodedRouteUrl[$i] ==  "{param}" )
                {
                    $params[] = $explodedRequestUrl[$i];
                    continue;
                }
                
                if ( $explodedRouteUrl[$i] != $explodedRequestUrl[$i] )
                {
                    continue 2;
                }
            }
            
            return array(
                "target" => $route["target"],
                "params" => $params
            );
        }
        
        return false;
    }
}
