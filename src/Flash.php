<?php
namespace Simox;

use Simox\DI\DIAwareInterface;

class Flash implements DIAwareInterface
{
	private $_di;

	private $_messages;

	public function __construct()
    {
    	$this->_di = null;

    	$this->_messages = array();
    }

    public function setDI( $di )
    {
    	$this->_di = $di;
    }

    public function getDI()
    {
    	return $this->_di;
    }

	/**
	 * Returns the messages
	 */
	public function output( $type = null )
	{
		$session = $this->_di->getService( "session" );

        /**
         * Get the messages from the session
         */
		$messages = $session->get( "__flash__" );

		/**
		 * If there are no messages to output -> exit early
		 */
        if ( $messages == null )
        {
        	return;
        }

        /**
         * If a type is specified -> implode and return the type messages
         */
        if ( isset($type) )
        {
            /**
             * Get the messages of the type
             */
        	$messages_type = implode( $messages[$type] );

            /**
             * Clear the type messages in the flash session
             */
        	$messages[$type] = array();

			$session->set( "__flash__", $messages );

    	    return $messages_type;
        }

        /**
         * Create a string with all messages
         */
        $all_messages_str = "";

        foreach ( $messages as $messages_type )
        {
        	$all_messages_str .= implode( $messages_type );
        }

        /**
         * When all the messages have been output -> clear all the messages in the flash session
         */
		$session->set( "__flash__", array() );

        return $all_messages_str;
	}

    /**
     * Private helper function to update the session.
     * Sets the messages in the session.
     */
    private function _updateSession()
    {
		$session = $this->_di->getService( "session" );

		$session->set( "__flash__", $this->_messages );
    }

	public function success( $message )
	{
		$message = "<div class='successMessage'>" . $message . "</div>";

		$this->_messages["success"][] = $message;

		$this->_updateSession();
	}

	public function error( $message )
	{
		$message = "<div class='errorMessage'>" . $message . "</div>";

		$this->_messages["error"][] = $message;

		$this->_updateSession();
	}

	public function notice( $message )
	{
		$message = "<div class='noticeMessage'>" . $message . "</div>";

		$this->_messages["notice"][] = $message;

		$this->_updateSession();
	}

	public function warning( $message )
	{
		$message = "<div class='warningMessage'>" . $message . "</div>";

		$this->_messages["warning"][] = $message;

		$this->_updateSession();
	}
}
