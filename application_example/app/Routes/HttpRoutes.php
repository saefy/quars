<?php
/*
|--------------------------------------------------------------------------
| Routing...
|--------------------------------------------------------------------------
|
| Route::all(); Automattically all requests will be chandled by
| http://mysupersite.com/{Controller}/{Method}/[additional-parameters]
|   
| Use Route::all(); or define rutes, you cannot use both.
| 
| Default route will be handled by \App\Controllers\IndexController->index()
|
*/
use \Quars\Route;


/*
|--------------------------------------------------------------------------
| Examples: Route all
|--------------------------------------------------------------------------
*/
Route::all(); // This will route all to controllers 


/*
|--------------------------------------------------------------------------
| Examples: Specifing Routes
|--------------------------------------------------------------------------
*/

// Route::any('/example/{name}?', function($name = ''){
// 	echo 'Hi, '.$name;
// });

// Route::any($uri, $callback);
// Route::get($uri, $callback);
// Route::post($uri, $callback);
// Route::put($uri, $callback);
// Route::patch($uri, $callback);
// Route::delete($uri, $callback);
// Route::options($uri, $callback);

// Route::any('/', function(){
// 	echo 'index';
// });
