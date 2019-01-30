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

use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
$router->post('login', function(Request $request, JWTAuth $jwt) {
    $this->validate($request, [
        'email' => 'required|email|exists:users',
        'password' => 'required|string'
    ]);
    if (! $token = $jwt->attempt($request->only(['email', 'password']))) {
        return response()->json(['user_not_found'], 404);
    }
    return response()->json(compact('token'));
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

$router->group([
  'middleware' => 'auth'
], function ($router) {
    $router->post('/private_data', function (JWTAuth $jwt) {
        $user = $jwt->parseToken()->toUser();
        return $user;
    });
});
