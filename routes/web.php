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
Route::post('/random/email/store', ['as' => 'random.email.store', 'uses' => 'RandomController@emailStore']);
Route::get('/email/statistic', 'EmailController@statistic');

$router->resource('domain', 'DomainController');
$router->resource('email', 'EmailController');
