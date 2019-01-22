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

$router->get('/', function () use ($router) {
  return $router->app->version();
});

$router->group(
  [
    'prefix' => '/v1'
  ], function ($router) {
    $router->group(
      [
        'prefix' => '/users'
      ], function ($router) {
        $router->get('/test', 'UsersController@test');
        $router->get('/welcome', 'UsersController@welcome');
        $router->post('/register', 'UsersController@register');
        $router->post('/login', 'UsersController@login');
      });
  }
);
