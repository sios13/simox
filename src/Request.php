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
	
	public function getPost( $var )
	{
		if ( isset($_POST[$var]) )
		{
			return $_POST[$var];
		}
		else
		{
			return false;
		}
	}
}
