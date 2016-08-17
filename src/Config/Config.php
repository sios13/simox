<?php
namespace Simox;

class Config implements \ArrayAccess
{
	public function __construct( $config_array = array() )
    {
        foreach ( $config_array as $key => $value )
        {
            $this->offsetSet( $key, $value );
        }
    }
    
    public function offsetExists( $key )
    {
        return isset($this->$key);
    }
    
    public function offsetGet( $key )
    {
        return $this->$key;
    }
    
    public function offsetSet( $key, $value )
    {
        if ( is_array($value) )
        {
            $this->$key = new self($value);
        }
        else
        {
            $this->$key = $value;
        }
    }
    
    public function offsetUnset( $key )
    {
        $this->$key = null;
    }
}
