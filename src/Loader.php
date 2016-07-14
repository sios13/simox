<?php
namespace Simox;

class Loader
{
    private $registered_directories;
    
    public function __construct()
    {
        $this->registered_directories = array();
    }
    
    public function _getVendorPath( $dir )
    {
        return realpath( getcwd() . DIRECTORY_SEPARATOR . $dir ) . DIRECTORY_SEPARATOR;
    }
    
    /**
     * Removes the namespace from a class
     */
    public function _getClassName( $namespace )
    {
        $exploded_namespace = explode( "\\", $namespace );
        
        return $exploded_namespace[count($exploded_namespace)-1];
    }
    
    public function _getNamespaceBase( $namespace )
    {
        $exploded_namespace = explode( "\\", $namespace );
        
        $namespace_base = "";
        
        for ( $i = 0; $i < count($exploded_namespace)-1; $i++ )
        {
            $namespace_base .= DIRECTORY_SEPARATOR . $exploded_namespace[$i];
        }
        
        return $namespace_base;
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
                if ( file_exists( $this->_getVendorPath($dir) . $this->_getNamespaceBase($name) . DIRECTORY_SEPARATOR . $this->_getClassName($name) . ".php" ) )
                {
                    include( realpath( $this->_getVendorPath($dir) . $this->_getNamespaceBase($name) . DIRECTORY_SEPARATOR . $this->_getClassName($name) . ".php" ) );
                    break;
                }
                else if ( file_exists( $this->_getVendorPath($dir) . $this->_getNamespaceBase($name) . DIRECTORY_SEPARATOR . $this->_getClassName($name) . DIRECTORY_SEPARATOR . $this->_getClassName($name) . ".php" ) )
                {
                    include( $this->_getVendorPath($dir) . $this->_getNamespaceBase($name) . DIRECTORY_SEPARATOR . $this->_getClassName($name) . DIRECTORY_SEPARATOR . $this->_getClassName($name) . ".php" );
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
