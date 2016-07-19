<?php
namespace Simox;

class Loader
{
    private $registered_directories;
    
    public function __construct()
    {
        $this->registered_directories = array();
    }
    
    public function registerDirs( $dirs )
    {
        foreach( $dirs as $dir )
        {
            $this->registered_directories[] = $dir;
        }
    }

    /**
     * Register autoloader
     */
    public function register()
    {
        spl_autoload_register( function($name) {
            foreach ( $this->registered_directories as $dir )
            {
                if ( file_exists($dir . DIRECTORY_SEPARATOR . $name . ".php") )
                {
                    include($dir . DIRECTORY_SEPARATOR . $name . ".php");
                }
            }
        } );
    }
}
