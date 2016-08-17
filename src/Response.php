<?php
namespace Simox;

use Simox\DI\DIAwareInterface;

class Response implements DIAwareInterface
{
    private $_di;

    private $_content;
    
    private $_headers;
    
	public function __construct() {}

    public function setDI( $di )
    {
        $this->_di = $di;
    }

    public function getDI()
    {
        return $this->_di;
    }
    
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
        $router = $this->_di->getService( "router" );

        $request = $this->_di->getService( "request" );

        if ( is_array($params) )
        {
            $uri = $router->reverseRoute( $params["controller"], $params["action"] );
        }

        $this->setStatusCode( 302, "Location: http://" . $request->getServer("HTTP_HOST") . $uri );
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
