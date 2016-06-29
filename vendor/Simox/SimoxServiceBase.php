<?php
namespace Simox;

abstract class SimoxServiceBase
{
    abstract function __construct();
    
    public function __get( $var_name )
    {
        return Simox::getService( $var_name );
    }
}
