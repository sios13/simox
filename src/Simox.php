<?php
namespace Simox;

class Simox
{
    private static $services = array();
    
    public function __construct()
    {
        self::$services = array(
            "view"       => function() {return new View();},
            "tag"        => function() {return new Tag();},
            "url"        => function() {return new Url();},
            "dispatcher" => function() {return new Dispatcher();},
            "router"     => function() {return new Router();},
            "session"    => function() {return new Session();},
            "request"    => function() {return new Request();},
            "flash"      => function() {return new Flash();},
            "cache"      => function() {return new Cache();},
            "response"   => function() {return new Response();}
        );
    }
    
    /**
     * Sets a service
     * 
     * @param string $service_name name of the service
     * @param closure $value
     */
    public function set( $service_name, $value )
    {
        self::$services[$service_name] = $value;
    }
    
    /**
     * Overrides the php magic function __get
     * If $var_name is a registered service, call the getService function
     * 
     * @var_name string
     */
    public function __get( $var_name )
    {
        if ( isset(self::$services[$var_name]) )
        {
            return self::getService( $var_name );
        }
    }
    
    /**
     * @param string $service_name
     * @return returns a Simox service
     */
    public static function getService( $service_name )
    {
        if ( is_callable(self::$services[$service_name]) )
        {
            self::$services[$service_name] = call_user_func( self::$services[$service_name] );
        }
        
        return self::$services[$service_name];
    }
    
    /**
     * The router starts matching the requested url with the routes.
     * The dispatcher handles the match result.
     */
    public function handle()
    {
        $this->router->handle();
        
        $this->dispatcher->setControllerName( $this->router->getControllerName() );
        $this->dispatcher->setActionName( $this->router->getActionName() );
        $this->dispatcher->setParams( $this->router->getParams() );
        
        $this->dispatcher->dispatch();
        
        $this->view->render();
        
        $this->response->setContent( $this->view->getContent() );
        
        $this->response->sendHeaders();
        //$this->response->sendCookies();
        
        return $this->response;
    }
}
