<?php
namespace Simox;

class Request extends SimoxServiceBase
{
    public function __construct() {}
    
	public function isPost()
	{
		if ($_SERVER["REQUEST_METHOD"] == "POST")
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function getPost( $name )
	{
		if ( isset($_POST[$name]) )
		{
			return $_POST[$name];
		}
		else
		{
			return false;
		}
	}
    
    public function getServer( $name )
    {
		if ( isset($_SERVER[$name]) )
		{
			return $_SERVER[$name];
		}
		else
		{
			return false;
		}
    }
}
