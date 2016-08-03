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
    
    public function redirect( $params )
    {
        if ( is_array($params) )
        {
            $uri = $this->router->reverseRoute( $params["controller"], $params["action"] );
        }
        
        $this->setStatusCode( 302, "Location: http://" . $this->request->getServer("HTTP_HOST") . $this->url->get($uri) );
    }
    
    public function sendHeaders()
    {
        if ( is_array($this->_headers) )
        {
            foreach ( $this->_headers as $header )
            {
                header( $header["message"], true, $header["code"] );
            }
        }
    }
}
