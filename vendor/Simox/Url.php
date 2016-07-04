<?php
namespace Simox;

class Url extends SimoxServiceBase
{
	protected $base_uri;
	
	public function __construct()
    {
        $this->base_uri = "/";
    }
	
    /**
     * Sets the base uri
     * 
     * @param string $base_uri
     */
	public function setBaseUri( $base_uri )
	{
		$this->base_uri = $base_uri;
	}
	
    /**
     * Returns the base uri
     * 
     * @return string
     */
	public function getBaseUri()
	{
		return $this->base_uri;
	}
    
    /**
     * Generate a URL appending the URI to the base URI
     * 
     * @param string $path
     * @return string
     */
    public function get( $path )
    {
        return $this->base_uri . $path;
    }
    
    /**
     * NOT IMPLEMENTED
     * Generate a URL for a static resource
     * 
     * @param string $path
     */
    public function getStatic( $path )
    {
        
    }
    
    public function getInstallPath()
    {
        return __DIR__;
    }
}
