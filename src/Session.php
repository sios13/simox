<?php
namespace Simox;

class Session extends SimoxServiceBase
{
	public function __construct() {}
    
    public function start()
    {
		session_start();
    }
    
    public function destroy()
    {
        $_SESSION = Array();
		session_destroy();
    }
	
	public function set( $name, $value )
	{
		$_SESSION[$name] = $value;
	}
	
	public function get( $name )
	{
		if ( isset($_SESSION[$name]) )
		{
			return $_SESSION[$name];
		}
		else
		{
			return false;
		}
	}
}
