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
     * Creates an HTML A tag using the Url Simox service
     *
     * <code>
     * <p>Hello, my name is Simon. Click <?php $this->tag->linkTo( "/blog/", "here" ); ?> to read my blog!</p>
     * <p>Check out my <?php $this->tag->linkTo( "http://www.example.com/", "awesome website", false ); ?>!</p>
     * </code>
     * 
     * @param string $path
     * @param string $text
     * @param boolean $local_path
     */
    public function linkTo( $path, $text, $local_path = true )
    {
        $basePath = "";
        
        if ( $local_path )
        {
            $basePath = $this->url->getBasePath();
        }
		
        $link = "<a href='$basePath$path'>$text</a>";
		
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
        $basePath = $this->url->getBasePath();
        
		$image_tag = "<img src='". $basePath . $path ."'";
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
	public function stylesheetLink( $path, $local_path = true )
	{
        $stylesheet_path = ($local_path ? $this->url->getBasePath() : "") . "public/" . $path;
        
        $stylesheet_tag = "<link href='" . $stylesheet_path . "' rel='stylesheet' type='text/css'>";
        
        return $stylesheet_tag;
	}
    
    /* ===== JAVASCRIPT ===== */
    /**
     * @param string $path path to the javascript file relative to the /public/ folder
     * @param boolean $local_path
     * @return string
     */
    public function javascriptInclude( $path, $local_path = true )
    {
        $javascript_path = ($local_path ? $this->url->getBasePath() : "") . $path;
        
        $javascript_tag = "<script src='" . $javascript_path . "'></script>";
        
        return $javascript_tag;
    }
    
}
