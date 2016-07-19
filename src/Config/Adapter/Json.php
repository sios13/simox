<?php
namespace Simox\Config\Adapter;

use Phalcon\Config

class Json extends Config
{
	public function __construct( $file_path )
    {
        parent::__construct( json_decode(file_get_contents($file_path)) );
    }
}
