<?php
namespace Simox;

abstract class SimoxServiceBase
{
    function __construct() {}
    
    public function __get( $var_name )
    {
        return Simox::getService( $var_name );
    }
}
