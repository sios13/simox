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
            "cache"      => function() {return new Cache();}
        );
    }
    
    public function set( $service_name, $value )
    {
        self::$services[$service_name] = $value;
    }
    
    public function __get( $var_name )
    {
        // if $var_name is a service
        return self::getService( $var_name );
    }
    
    public static function getService( $service_name )
    {
        if ( isset(self::$services[$service_name]) )
        {
            if ( is_callable(self::$services[$service_name]) )
            {
                self::$services[$service_name] = call_user_func( self::$services[$service_name] );
            }
            
            return self::$services[$service_name];
        }
    }
    
    public function getContent()
    {
        $this->dispatcher->dispatch( $this->router->match() );
        
        $content = $this->view->render();
        
        return $content;
    }
}
