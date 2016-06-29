<?php
namespace Simox;

class Tag extends SimoxServiceBase
{
	public function __construct() {}
    
    /* ===== TITLE ===== */
    
    private $title;
    private $title_append;
    private $title_prepend;

    public function setTitle( $title )
    {
        $this->title = $title;
    }
    
    public function prependTitle( $title_prepend )
    {
        $this->title_prepend = $title_prepend;
    }
    
    public function appendTitle( $title_append )
    {
        $this->title_append = $title_append;
    }
    
    public function getTitle()
    {
        return "<title>" . $this->title . $this->title_prepend . $this->title_append . "</title>";
    }
    
    /* ===== LINKS ===== */
    
    public function linkTo( $target, $text, $local_path = true )
    {
        $basePath = "";
        if ($local_path)
        {
            $basePath = $this->url->getBaseUri();
        }
		
        $link = "<a href='$basePath$target' ";
		
        $link .= ">$text</a>";
		
        return $link;
    }
	
	/* ==== IMAGES ==== */
	
	public function image( $path, $alt, $params = array() )
	{
        $basePath = $this->url->getBaseUri();
        
		$image = "<img src='". $basePath . $path ."'";
        $image .= " alt='". $alt ."'";
		
        if ( isset($params["style"]) )
        {
            $image .= " style='". $params["style"] ."'";
        }
		
        if ( isset($params["class"]) )
        {
            $image .= " class='". $params["class"] ."'";
        }
		
        $image .= ">";
        
		return $image;
	}
    
    /* ===== STYLESHEET ===== */
	
	public function stylesheetLink( $path, $local_path = true )
	{
        $stylesheet_path = ($local_path ? $this->url->getBaseUri() : "") . $path;
        
        $stylesheet_link = "<link href='" . $stylesheet_path . "' rel='stylesheet' type='text/css'>";
        
        return $stylesheet_link;
	}
    
    /* ===== JAVASCRIPT ===== */
    
    public function javascriptInclude( $path, $local_path = true )
    {
        $javascript_path = ($local_path ? $this->url->getBaseUri() : "") . $path;
        
        $javascript_link = "<script src='" . $javascript_path . "'></script>";
        
        return $javascript_link;
    }
    
}
