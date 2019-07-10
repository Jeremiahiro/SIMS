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

$router->post('api/register', 'RegisterController@register');
$router->post('api/verify', 'VerificationController@verify');
$router->post('api/login', 'AuthController@login');

Route::post('api/recovery', 'VerificationController@recovery');
Route::put('api/reset', 'VerificationController@reset');


Route::group(['middleware' => 'jwt.auth', 'prefix' => 'api'], function() use ($router) {
    $router->put('/edit', 'ProfileController@update');
    $router->post('/upload', 'ProfileController@uploadImage');
});