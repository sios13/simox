<?php
namespace Simox;

class Router extends SimoxServiceBase
{
    private $routes;
    
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
    public function addRoute($path, $target)
    {
        if ( !is_array($target) )
        {
            $target = explode( "#", $target );
            $controller_name = str_replace( "Controller", "", $target[0] );
            $action_name = str_replace( "Action", "", $target[1] );
        }
        else
        {
            $controller_name = $target["controller"];
            $action_name = $target["action"];
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

        foreach ($this->_routes as $route)
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
            
            $this->_controller_name = $route["controller_name"];
            $this->_action_name = $route["action_name"];
            $this->_params = $params;
        }
    }
}
