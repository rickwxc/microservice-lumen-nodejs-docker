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
    return $router->app->version();
});

$router->group(
    ['middleware' => 'jwt.auth'], 
    function() use ($router) {
        $router->post('/stores-protected-data', function(Request $request) {
            return 'jwt works: '.json_encode($request->auth);
        });
    }
);

$router->group(
  [
    'middleware' => 'jwt.auth',
    'prefix' => '/v1'
  ], function ($router) {
    $router->group(
      [
        'prefix' => '/stores'
      ], function ($router) {
        $router->post('/', 'StoresController@create');
        $router->get('/', 'StoresController@index');
        $router->get('/{id:[\d]+}', [
          'as' => 'stores.show',
          'uses' => 'StoresController@show'
        ]);
        $router->put('/{id:[\d]+}', 'StoresController@update');
        $router->delete('/{id:[\d]+}', 'StoresController@destroy');

        $router->get('/{storeId:[\d]+}/branches', 'BranchesController@getBranch');
        $router->post('/{storeId:[\d]+}/branches', 'BranchesController@addBranch');
      });
  }
);

