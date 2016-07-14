<?php

namespace Simox;

abstract class Controller extends SimoxServiceBase
{
    public function __construct() {}
    
    public function initialize(){}
    
    public function redirect( $path, $is_local_path = true )
    {
        if ( $is_local_path )
        {
            $full_path = $this->url->get( $path );
            
            // Remove excess slash
            $full_path = preg_replace('#/+#','/', $full_path);
        }
        else
        {
            $full_path = $path;
        }
        
        header( "Location: $full_path" );
        die();
    }
}
