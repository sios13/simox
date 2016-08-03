<?php
namespace Simox;

class Url extends SimoxServiceBase
{
    private $_root_path;
    
    /**
     * Base uri is prepended to all resources (css, images, links..)
     */
	private $_uri_prefix;
	
	public function __construct()
    {
        $this->_uri_prefix = "/";
        
        $this->_root_path = realpath(__DIR__ . "/../../../../");
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
        $uri_prefix = preg_replace( "#/+#", "/", "/" . $uri_prefix . "/" );
        
		$this->_uri_prefix = $uri_prefix;
	}
	
    /**
     * Returns the base uri
     * 
     * @return string
     */
	public function getUriPrefix()
	{
		return $this->_uri_prefix;
	}
    
    /**
     * Appends a given path to the base uri
     * 
     * @param string $path
     * @return string
     */
    public function get( $path )
    {
        return preg_replace( "#/+#", "/", $this->getUriPrefix() . $path );
    }
}
