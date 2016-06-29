<?php

use Simox\Controller;

class IndexController extends Controller
{
    public function initialize()
    {
        $this->tag->prependTitle( " - " );
        $this->tag->appendTitle( "simonwebb.se" );
        
        $this->view->setMainView( "default" );
    }
    
    public function indexAction()
    {
        $this->tag->setTitle( "Billiga hemsidor" );
        
        //$this->view->enableCache( array("key" => "index-cache", "level" => 5) );
    }
    
    public function notfoundAction()
    {
        $this->tag->setTitle( "Sidan finns inte" );
        $this->view->pick( "posts/404" );
    }
}
