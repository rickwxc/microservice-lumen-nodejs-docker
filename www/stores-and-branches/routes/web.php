<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Illuminate\Http\Request;

$router->get('/', function () use ($router) {
    return 'stores is here, '.$router->app->version();
});

$router->get('/stores/echo', function () use ($router) {
  return 'hi, stores is here via GET';
});

$router->post('/stores-echo', function () use ($router) {
  return 'hi, stores is here via POST';
});


$router->group(
    ['middleware' => 'jwt.auth'], 
    function() use ($router) {
        $router->post('/stores-protected-data', function(Request $request) {
            return 'Greate, jwt works: '.json_encode($request->auth);
        });
    }
);
