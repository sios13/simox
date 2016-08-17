<?php
namespace Simox;

class Request
{
    public function __construct() {}
    
	public function isPost()
	{
		if ( $_SERVER["REQUEST_METHOD"] == "POST" )
		{
			return true;
		}

		return false;
	}
	
	public function getPost( $name )
	{
		if ( isset($_POST[$name]) )
		{
			return $_POST[$name];
		}
	
		return false;
	}
    
    public function getServer( $name )
    {
		if ( isset($_SERVER[$name]) )
		{
			return $_SERVER[$name];
		}
	
		return false;
    }
    
    public function getRequestUri()
    {
        return $this->getServer( "REQUEST_URI" );
    }
}
