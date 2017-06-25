<?php
namespace Simox;

use Simox\DI\DIAwareInterface;

class Url implements DIAwareInterface
{
    /**
     * Root path point to the root folder (assuming composer!!)
     */
    private $_root_path;

    /**
     * Uri prefix is prepended to all resources (css, images, links..)
     */
	private $_uri_prefix;

	public function __construct()
    {
        $this->_root_path = realpath( __DIR__ . "/../../../../" );

        $this->_uri_prefix = "";
    }

    public function setDI( $di )
    {
        $this->_di = $di;
    }

    public function getDI()
    {
        return $this->_di;
    }

    /**
     * Returns a formatted uri.
     * Using this all uri:s will have a consistent format.
     */
    // public function _format( $uri )
    // {
    //     return preg_replace( "#/+#", "/", "/" . $uri );
    // }

    /**
     * Returns the root path
     */
    public function getRootPath()
    {
        return $this->_root_path;
    }

    /**
     * Sets the uri prefix
     *
     * @param string $uri_prefix
     */
	public function setUriPrefix( $uri_prefix )
	{
		$this->_uri_prefix = $uri_prefix;
	}

    /**
     * Returns the uri prefix
     *
     * @return string
     */
	public function getUriPrefix()
	{
		return $this->_uri_prefix;
	}

    /**
     * Returns a given path appended to the uri prefix
     *
     * @param string $path
     * @return string
     */
    public function get( $path )
    {
        return $this->getUriPrefix() . $path;
    }
}
