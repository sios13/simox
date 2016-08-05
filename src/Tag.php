<?php
namespace Simox;

class Tag extends SimoxServiceBase
{
	public function __construct() {}
    
    /* ===== TITLE ===== */
    
    private $_title;
    private $_title_append;
    private $_title_prepend;

    /**
     * Sets a title
     * @param string $title
     */
    public function setTitle( $title )
    {
        $this->_title = $title;
    }
    
    /**
     * Sets a prepend title
     * @param string $title_prepend
     */
    public function prependTitle( $title_prepend )
    {
        $this->_title_prepend = $title_prepend;
    }
    
    /**
     * Sets a append title
     * @param string $title_append
     */
    public function appendTitle( $title_append )
    {
        $this->_title_append = $title_append;
    }
    
    /**
     * @return string returns a title tag
     */
    public function getTitle()
    {
        return "<title>" . $this->_title . $this->_title_prepend . $this->_title_append . "</title>";
    }
    
    /* ===== LINKS ===== */
    
    /**
     * Creates an HTML anchor tag using the Url Simox service
     *
     * <code>
     * <p>Hello, my name is Simon. Click <?php $this->tag->linkTo( "/blog/", "here" ); ?> to read my blog!</p>
     * <p>Check out my <?php $this->tag->linkTo( "http://www.example.com/", "awesome website", false ); ?>!</p>
     * </code>
     * 
     * @param string $path
     * @param string $text
     * @param boolean $is_local_path
     */
    public function linkTo( $path, $text, $is_local_path = true )
    {
        if ( $is_local_path )
        {
            $path = $this->url->get( $path );
        }
		
        $link = "<a href='$path'>$text</a>";
		
        return $link;
    }
	
	/* ==== IMAGES ==== */
	/**
     * @param string $path path to the image relative to the /public/ folder
     * @param string $alt alternative text to the img-tag
     * @param array $options
     * @return string returns an HTML img tag
     */
	public function image( $path, $alt, $options = array() )
	{
        $path = $this->url->get( $path );
        
		$image_tag = "<img src='". $path ."'";
        $image_tag .= " alt='". $alt ."'";
		
        if ( isset($options["style"]) )
        {
            $image_tag .= " style='". $options["style"] ."'";
        }
		
        if ( isset($options["class"]) )
        {
            $image_tag .= " class='". $options["class"] ."'";
        }
		
        $image_tag .= ">";
        
		return $image_tag;
	}
    
    /* ===== STYLESHEET ===== */
	/**
     * @param string $path path to the stylesheet realtive to the /public/ folder
     * @param boolean $local_path
     * @return string 
     */
	public function stylesheetLink( $path, $is_local_path = true )
	{
        if ( $is_local_path )
        {
            $path = $this->url->get( $path );
        }
        
        $stylesheet_tag = "<link href='" . $path . "' rel='stylesheet' type='text/css'>";
        
        return $stylesheet_tag;
	}
    
    /* ===== JAVASCRIPT ===== */
    /**
     * @param string $path path to the javascript file relative to the /public/ folder
     * @param boolean $local_path
     * @return string
     */
    public function javascriptInclude( $path, $is_local_path = true )
    {
        if ( $is_local_path )
        {
            $path = $this->url->get( $path );
        }
        
        $javascript_tag = "<script src='" . $path . "'></script>";
        
        return $javascript_tag;
    }
    
}
