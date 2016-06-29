<?php

/**
 * SIMOX - Simple PHP MVC Framework
 */

define( "SIMOX_START", microtime() );

ini_set('display_errors', 1); 
error_reporting(E_ALL);

use Simox\Simox;
use Simox\Loader;
use Simox\Url;
use Simox\View;
use Simox\Router;
//use Simox\Database\Mysql as MysqlConnection;
use Simox\Database\Sqlite as SqliteConnection;
use Simox\Dispatcher;
use Simox\EventsManager;

try {
    require( getcwd() . "/../vendor/Simox/Loader.php" );
    $loader = new Loader();
    $loader->registerDirs( array(
        "../vendor",
        "../app/controllers",
        "../app/models",
        "../app/plugins"
    ) );
    $loader->register();
    
    $simox = new Simox();

    $simox->set( "url", function() {
        $url = new Url();
        $url->setBaseUri( "/simonwebb/" );
        return $url;
	} );

    $simox->set( "view", function() {
        $view = new View();
        $view->setViewsDir( "../app/views/" );
        //$view->setMainView( "default" );
        return $view;
    } );

    $simox->set( "router", function() {
        $router = new Router();
        $router->addRoute( "/", "IndexController#indexAction" );
        $router->addRoute( "/project/simoxbook", "ProjectController#simoxbookAction" );
        $router->addRoute( "/project/simox", "ProjectController#simoxAction" );
        $router->addRoute( "/project/konstochbruksglasforeningen", "ProjectController#konstochbruksglasforeningenAction" );
        $router->addRoute( "/project/rentalmovies", "ProjectController#rentalmoviesAction" );
        $router->addRoute( "/project/simox/{param}/test", "IndexController#testAction" );
        //$router->addRoute( "/project/simox/{param}", function($param) {$this->view->setMainView("default"); echo "HEJ " . $param;} );
        return $router;
    } );
    
    $simox->set( "database", function() {
        $connection = new SqliteConnection(array(
            "dbname" => "db.db"
        ));
        return $connection;
    } );
	
	$simox->set( "dispatcher", function() {
		//$eventsManager = new EventsManager();
		//$eventsManager->attach( "dispatch:beforeDispatch", new SecurityPlugin() );
        
		$dispatcher = new Dispatcher();
		//$dispatcher->setEventsManager( $eventsManager );
        $dispatcher->setNoMatchPath( "IndexController#NotfoundAction" );
		return $dispatcher;
	} );
    
    echo $simox->getContent();

} catch( \Exception $e ) {
    echo "SimoxException: ", $e->getMessage();
}

echo "Time to run: " . (microtime() - SIMOX_START);
