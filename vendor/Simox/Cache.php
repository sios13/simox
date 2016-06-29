<?php
namespace Simox;

class Cache extends SimoxServiceBase
{
    private $cache_dir;
    
    private $current_stream_filename;
    private $current_stream_content;

    public function __construct()
    {
        $this->cache_dir = realpath( $this->url->getInstallPath() . "/../../" ) . "\app\cache\\";
    }
    
    /**
    * Deletes the cache file with the given key
    */
    public function delete( $key )
    {
        unlink( $this->cache_dir . $key . ".html" );
    }
    
    /**
    * Returns true of cache with given key exists, otherwise false
    * If the cache has lived for longer than its life time, destroy the cache
    */
    public function exists( $key, $lifetime )
    {
        if ( !file_exists($this->cache_dir . $key . ".html") )
        {
            return false;
        }
        
        $file_lifetime = time() - filectime( $this->cache_dir . $key . ".html" );
        
        if ( $file_lifetime > $lifetime )
        {
            $this->delete( $key );
            return false;
        }
        
        return true;
    }
    
    public function get( $key )
    {
        if ( file_exists($this->cache_dir . $key . ".html") )
        {
            include ($this->cache_dir . $key . ".html");
        }
    }
    
    public function start( $filename )
    {
        $this->current_stream_filename = $filename;
        
        ob_start();
    }
    
    public function save()
    {
        $this->current_stream_content = ob_get_contents();
        
        ob_end_clean();
        
        file_put_contents( $this->cache_dir . $this->current_stream_filename . ".html", $this->current_stream_content );
    }
}