<?php
/**
 * Quars - Framework
 *
 * @package  Quars
 * @author   Miguel Mendoza <mmendoza000@gmail.com>
 */
 
namespace Quars;

use Phroute\Phroute\RouteCollector;

Global $Router, $RouteMode;

$Router = new RouteCollector();
$RouteMode = 'ROUTE'; // ROUTE | ALL

class Route{
	
	public static function all(){
		Global $RouteMode;
		$RouteMode = 'ALL';
	}

	public static function any($uri, $callback){
		Global $Router, $RouteMode;
		$Router->any($uri, $callback);
		$RouteMode = 'ROUTER';
	}

	public static function get($uri, $callback){
		Global $Router, $RouteMode;
		$Router->get($uri, $callback);
		$RouteMode = 'ROUTER';
	
	}
	public static function post($uri, $callback){
		Global $Router, $RouteMode;
		$Router->post($uri, $callback);
		$RouteMode = 'ROUTER';
	}

	public static function put($uri, $callback){
		Global $Router, $RouteMode;
		$Router->put($uri, $callback);
		$RouteMode = 'ROUTER';
	}

	public static function patch($uri, $callback){
		Global $Router, $RouteMode;
		$Router->patch($uri, $callback);
		$RouteMode = 'ROUTER';
	}

	public static function delete($uri, $callback){
		Global $Router, $RouteMode;
		$Router->delete($uri, $callback);
		$RouteMode = 'ROUTER';
	}

	public static function options($uri, $callback){
		Global $Router, $RouteMode;
		$Router->options($uri, $callback);
		$RouteMode = 'ROUTER';
	}

	public static function getRouterObject(){
		Global $Router;
		return $Router->getData();
	}
}
