<?php
namespace Simox;

class Url extends SimoxServiceBase
{
    /**
     * Base path is the path to the project install folder relative to the server root
     */
	private $_base_uri;
    
    /**
     * Base path prepend is prepended to the base path
     */
    private $_base_uri_prepend;
	
	public function __construct()
    {
        $this->_base_uri = "/";
        
        /**
         * This is the base path prepend when using composer
         */
        $this->_base_uri_prepend = "/../../../../../";
    }
	
    /**
     * Sets the base uri
     * 
     * @param string $base_uri
     */
	public function setBaseUri( $base_uri )
	{
        /**
         * If there is no prepending slash, add it
         */
        if ( $base_uri[0] !== "/" )
        {
            $base_uri = "/" . $base_uri;
        }
        
        /**
         * If there is no appending slash, add it
         */
        if ($base_uri[strlen($base_uri)-1] !== "/")
        {
            $base_uri = $base_uri . "/";
        }
        
		$this->_base_uri = $base_uri;
	}
	
    /**
     * Returns the base path
     * 
     * @return string
     */
	public function getBaseUri()
	{
		return $this->_base_uri;
	}
    
    public function setBaseUriPrepend( $path )
    {
        $this->_base_uri_prepend = $path;
    }
    
    public function getBaseUriPrepend()
    {
        return $this->_base_uri_prepend;
    }
    
    public function getBasePath()
    {
        return $this->_base_uri_prepend . $this->_base_uri;
    }
    
    /**
     * Appends a given path to the base uri
     * 
     * @param string $path
     * @return string
     */
    public function get( $path )
    {
        return $this->getBaseUri() . $path;
    }
}
