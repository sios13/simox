<?php
namespace Simox;

use Simox\DI\DIAwareInterface;

abstract class Controller implements DIAwareInterface
{
	private $_di;

    public function __construct() {}

    public function setDI( $di )
    {
    	$this->_di = $di;
    }

    public function getDI()
    {
    	return $this->_di;
    }

    public function __get( $var_name )
    {
        if ( $this->_di->hasService($var_name) )
        {
            return $this->_di->getService( $var_name );
        }

        if ( $var_name == "di" )
        {
            return $this->_di;
        }
    }
    
    public function initialize() {}
}
