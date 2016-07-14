<?php
namespace Simox;

class Flash extends SimoxServiceBase
{
	public function __construct()
    {
        $this->session->set( "__flash__", null );
    }
	
	public function output()
	{
        return $this->session->get( "__flash__" );
	}
	
	public function success( $message )
	{
		$this->session->set( "__flash__", $this->session->get( "__flash__" ) . "<div class='successMessage'>" . $message . "</div>" );
	}
	
	public function error( $message )
	{
		$this->session->set( "__flash__", $this->session->get( "__flash__" ) . "<div class='errorMessage'>" . $message . "</div>" );
	}
	
	public function notice( $message )
	{
		$this->session->set( "__flash__", $this->session->get( "__flash__" ) . "<div class='noticeMessage'>" . $message . "</div>" );
	}
	
	public function warning( $message )
	{
		$this->session->set( "__flash__", $this->session->get( "__flash__" ) . "<div class='warningMessage'>" . $message . "</div>" );
	}
}
