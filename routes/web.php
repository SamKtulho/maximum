<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');
Route::get('/random/email', ['as' => 'random.email', 'uses' => 'RandomController@email']);

Route::get('/moderator/links', ['uses' => 'ModeratorController@link']);
Route::post('/moderator/vote', ['as' => 'moderator.vote', 'uses' => 'ModeratorController@vote']);

Route::post('/random/email/store', ['as' => 'random.email.store', 'uses' => 'RandomController@emailStore']);
Route::get('/random/link', ['as' => 'random.link', 'uses' => 'RandomController@link']);
Route::post('/random/link/store', ['as' => 'random.link.store', 'uses' => 'RandomController@linkStore']);
Route::get('/email/statistic', 'EmailController@statistic');
Route::post('/email/statistic/data', 'EmailController@data');
Route::get('/email/count', 'EmailController@count');
Route::get('/link/statistic', 'LinkController@statistic');
Route::get('/link/count', 'LinkController@count');

$router->resource('domain', 'DomainController');
$router->resource('email', 'EmailController');
$router->resource('link', 'LinkController');
