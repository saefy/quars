<?php
namespace App\Controllers;

use \Quars\Controller;
use \Quars\ActiveRecord As Model;
use \Quars\Bladex;

use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;

class IndexController extends Controller{
    
    public function index(){

        // Example with ActiveRecord
    	$User = new \Quars\ActiveRecord('usuarios');

    	$User->find(4);
    	
        // user data is in $User->fields;

    	$blade = new Bladex();
    	
    	$data = [
    		'name' => 'John Doe',
    		'message' => 'This is rendered with blade template engine',
    		'user' => $User->fields
    	];
		echo $blade->make('homepage', $data);

    } // End index

} // End IndexController