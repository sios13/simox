<?php
namespace Simox;

use Simox\DI\DIAwareInterface;

class Simox implements DIAwareInterface
{
    private $_di;
    
    public function __construct( $di )
    {
        $this->_di = $di;
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
     * Overrides the php magic function __get
     * If $var_name is a registered service in the DI -> return the service
     * 
     * @var_name string
     */
    public function __get( $var_name )
    {
        if ( $this->_di->hasService($var_name) )
        {
            return $this->_di->getService( $var_name );
        }
    }

    public function handle()
    {
        /**
         * Register the autoloader
         */
        $this->loader->register();
        
        /**
         * The router handles the request uri
         */
        $this->router->handle( $this->request->getRequestUri() );

        /**
         * Set the matched route in the dispatcher
         */
        $this->dispatcher->setRoute( $this->router->getMatchRoute() );

        /**
         * Start the dispatch
         */
        $this->dispatcher->dispatch();

        /**
         * Render the view
         */
        $this->view->render();

        /**
         * Set the content from the view as content in the response service
         */
        $this->response->setContent( $this->view->getContent() );
        
        /**
         * Send the headers
         */
        $this->response->sendHeaders();

        /**
         * Send the cookies (NYI)
         */
        //$this->response->sendCookies();

        /**
         * Return the response service
         */
        return $this->response;
    }
}
