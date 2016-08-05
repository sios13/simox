<?php
namespace Simox;

class Url extends SimoxServiceBase
{
    private $_root_path;
    
    /**
     * Uri prefix is prepended to all resources (css, images, links..)
     */
	private $_uri_prefix;
	
	public function __construct()
    {
        $this->_uri_prefix = null;
        
        $this->_root_path = realpath( __DIR__ . "/../../../../" );
    }

    /**
     * Returns a formatted uri.
     * Gives all uri:s a consistent format.
     */
    public function _format( $uri )
    {
        return preg_replace( "#/+#", "/", "/" . $uri );
    }
    
    public function getRootPath()
    {
        return $this->_root_path;
    }
	
    /**
     * Sets the uri prefix
     * 
     * @param string $uri_prefix
     */
	public function setUriPrefix( $uri_prefix )
	{
		$this->_uri_prefix = $this->_format( $uri_prefix . "/" );
	}
	
    /**
     * Returns the uri prefix
     * 
     * @return string
     */
	public function getUriPrefix()
	{
		return $this->_uri_prefix;
	}
    
    /**
     * Returns a given path appended to the uri prefix
     * 
     * @param string $path
     * @return string
     */
    public function get( $path )
    {
        return $this->_format( $this->getUriPrefix() . $path );
    }
}
