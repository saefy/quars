<?php
/**
 * Quars - Framework
 *
 * @package  Quars
 * @author   Miguel Mendoza <mmendoza000@gmail.com>
 */
 
namespace Quars;

use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;

class Request {
	public static function Serve(){
		$dispatcher =  new Dispatcher(Route::getRouterObject());
		try {
			echo $response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		} catch (HttpRouteNotFoundException | HttpMethodNotAllowedException $e) {
		   	// Error:(404)
		    header('HTTP/1.0 404 Not Found');
		    \Quars\Quars::_use('app/Errors/404.php', '',true,'../src/Quars/Messages/Page/404.php');
		}		
	}
}
