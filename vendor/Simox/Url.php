<?php
namespace Simox;

class Url extends SimoxServiceBase
{
	protected $base_uri;
	
	public function __construct()
    {
        $this->base_uri = "/";
    }
	
	public function setBaseUri( $uri )
	{
		$this->base_uri = $uri;
	}
	
	public function getBaseUri()
	{
		return $this->base_uri;
	}
    
    /* Generate a URL appending the URI to the base URI */
    public function get( $path )
    {
        return $this->base_uri . $path;
    }
    
    /* Generate a URL for a static resource */
    public function getStatic( $path )
    {
        
    }
    
    public function getInstallPath()
    {
        return __DIR__;
    }
}
