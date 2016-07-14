<?php
namespace Simox;

class Response extends SimoxServiceBase
{
    private $_content;
    
    private $_headers;
    
	public function __construct() {}
    
    public function setContent( $content )
    {
        $this->_content = $content;
    }
    
    public function getContent()
    {
        return $this->_content;
    }
    
    public function setStatusCode( $code, $message )
    {
        $this->_headers[] = array("code" => $code, "message" => $message);
    }
    
    public function sendHeaders()
    {
        if ( is_array($this->_headers) )
        {
            foreach ( $this->_headers as $header )
            {
                header( "HTTP/1.1 " . $header["code"] . " " . $header["message"], true, $header["code"] );
            }
        }
    }
}
