<?php
namespace Simox;

class DI
{
	private static $_default;

	private $_services;

	public function __construct()
	{
		self::$_default = $this;

		$this->_services = array(
            "view"       => function() {return new View();},
            "tag"        => function() {return new Tag();},
            "url"        => function() {return new Url();},
            "dispatcher" => function() {return new Dispatcher();},
            "router"     => function() {return new Router();},
            "session"    => function() {return new Session();},
            "request"    => function() {return new Request();},
            "flash"      => function() {return new Flash();},
            "cache"      => function() {return new Cache();},
            "response"   => function() {return new Response();},
            "loader"     => function() {return new Loader();}
		);
	}

	public function hasService( $service_name )
	{
		if ( is_callable($this->_services[$service_name]) )
		{
			return true;
		}

		return false;
	}

	public function getService( $service_name )
	{
		/**
		 * If trying to get a service that does not exist -> exit early
		 */
		if ( !$this->hasService( $service_name ) )
		{
			return;
		}

		/**
		 * If the service has already been instanced -> return the service
		 */
		if ( isset($this->$service_name) )
		{
			return $this->$service_name;
		}

		$service_definition = $this->_services[$service_name];

        $service_instance = call_user_func( $service_definition );

        /**
         * Inject the di into the service.
         * The di might already be set by the user, but we set it again to make sure.
         */
        if ( $service_instance instanceof DI\DIAwareInterface )
        {
        	$service_instance->setDI( $this );
        }

        /**
         * Set the service as a property, making sure we do not instance the same service twice
         */
        $this->$service_name = $service_instance;

        return $service_instance;
	}

	public function set( $service_name, $definition )
	{
		$this->_services[$service_name] = $definition;
	}

	public static function getDefault()
	{
		return self::$_default;
	}
}
