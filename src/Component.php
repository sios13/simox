<?php
namespace Simox;

abstract class Component
{
	public function __construct() {}

	public function __get( $var_name )
	{
		$di = DI::getDefault();

        if ( $di->hasService($var_name) )
        {
            return $di->getService( $var_name );
        }

        if ( $var_name == "di" )
        {
            return $di;
        }
	}
}
