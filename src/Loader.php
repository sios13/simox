<?php
namespace Simox;

class Loader
{
    private $registered_directories;
    
    public function __construct()
    {
        $this->registered_directories = array();
    }
    
    /**
    * The autoloader will look in the registered directories
    */
    public function registerDirs( $dirs )
    {
        foreach( $dirs as $dir )
        {
            $this->registered_directories[] = $dir;
        }
    }

    public function register()
    {
        // Register autoloader
        spl_autoload_register( function($name) {
            foreach( $this->registered_directories as $dir )
            {
                if ( file_exists( realpath( getcwd() . DIRECTORY_SEPARATOR . $dir ) . DIRECTORY_SEPARATOR . str_replace( "\\", "/", $name ) . ".php" ) )
                {
                    include( realpath( realpath( getcwd() . DIRECTORY_SEPARATOR . $dir ) . DIRECTORY_SEPARATOR . str_replace( "\\", "/", $name ) . ".php" ) );
                    break;
                }
            }
        } );
        
        // Include vendor / composer autoloader
        if ( file_exists(__DIR__ . "/../autoload.php") )
        {
            include( __DIR__ . "/../autoload.php" );
        }
    }
}
