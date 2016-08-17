<?php
namespace Simox;

use Simox\DI\DIAwareInterface;

class Loader implements DIAwareInterface
{
    private $_di;

    /**
     * The autoloader will search the registered directories
     */
    private $registered_directories;
    
    public function __construct()
    {
        $this->_di = null;

        $this->registered_directories = array();
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
     * Directories are registered relative to the project root
     */
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
        $url = $this->_di->getService( "url" );

        $base_path = $url->getRootPath();

        spl_autoload_register( function($file_name) use ($base_path) {
            foreach ( $this->registered_directories as $dir )
            {
                $file_path = $base_path . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $file_name . ".php";
                
                if ( file_exists($file_path) )
                {
                    return include($file_path);
                }
            }
        } );
    }
}
