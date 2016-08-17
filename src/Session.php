<?php
namespace Simox;

class Session
{
	public function __construct() {}
    
    /**
     * Starts the session
     */
    public function start()
    {
		session_start();
    }
    
    /**
     * Destroys the session
     */
    public function destroy()
    {
        $_SESSION = array();

		session_destroy();
    }
	
    /**
     * Sets the session
     *
     * @param string $key
     * @param string $value
     */
	public function set( $key, $value )
	{
		$_SESSION[$key] = $value;
	}
	
    /**
     * Returns the session
     *
     * @param string $key
     */
	public function get( $key )
	{
		if ( isset($_SESSION[$key]) )
		{
			return $_SESSION[$key];
		}
        
		return null;
	}
}
