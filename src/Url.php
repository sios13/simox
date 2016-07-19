<?php
namespace Simox;

class Url extends SimoxServiceBase
{
    /**
     * Base uri is prepended to all resources (css, images, links..)
     */
	private $_base_uri;
	
	public function __construct()
    {
        $this->_base_uri = "/";
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
     * Returns the base uri
     * 
     * @return string
     */
	public function getBaseUri()
	{
		return $this->_base_uri;
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
    
    public function getRootPath()
    {
        return "../";
    }
}
