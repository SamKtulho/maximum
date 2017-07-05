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

Route::get('/moderator/links', ['uses' => 'ModeratorController@link']);
Route::get('/moderator/emails', ['uses' => 'ModeratorController@email'])->middleware('is_moderator');
Route::get('/moderator/subdomains', ['uses' => 'ModeratorController@subdomain']);
Route::post('/moderator/vote', ['as' => 'moderator.vote', 'uses' => 'ModeratorController@vote']);
Route::post('/moderator/vote_email', ['as' => 'moderator.voteEmail', 'uses' => 'ModeratorController@voteEmail'])->middleware('is_moderator');
Route::post('/moderator/vote_subdomain', ['as' => 'moderator.voteSubdomain', 'uses' => 'ModeratorController@voteSubdomain']);
Route::post('/moderator/change_vote', ['uses' => 'ModeratorController@changeVote']);
Route::get('/moderator/report', ['uses' => 'ModeratorController@report'])->middleware('is_admin');

Route::get('/random/email', ['as' => 'random.email', 'uses' => 'RandomController@email'])->middleware('is_moderator');
Route::post('/random/email/store', ['as' => 'random.email.store', 'uses' => 'RandomController@emailStore'])->middleware('is_moderator');
Route::get('/random/link', ['as' => 'random.link', 'uses' => 'RandomController@link']);
Route::post('/random/link/store', ['as' => 'random.link.store', 'uses' => 'RandomController@linkStore']);
Route::get('/random/manualDomain', ['as' => 'random.manualDomain', 'uses' => 'RandomController@manualDomain']);

Route::post('/random/manualDomain/store', ['as' => 'random.manualDomain.store', 'uses' => 'RandomController@manualDomainStore']);
Route::get('/random/manualSubdomain', ['as' => 'random.manualSubdomain', 'uses' => 'RandomController@manualSubdomain']);
Route::post('/random/manualSubdomain/store', ['as' => 'random.manualSubdomain.store', 'uses' => 'RandomController@manualSubdomainStore']);

Route::get('/email/statistic', 'EmailController@statistic')->middleware('is_moderator');
Route::post('/email/statistic/data', 'EmailController@data')->middleware('is_moderator');
Route::get('/email/count', 'EmailController@count')->middleware('is_moderator');
Route::get('/email/moderation_log', 'EmailController@moderationLog')->middleware('is_moderator');
Route::post('/email/moderation_log/data', 'EmailController@moderationLogData')->middleware('is_moderator');

Route::get('/link/statistic', 'LinkController@statistic');
Route::post('/link/statistic/data', 'LinkController@data');
Route::get('/link/count', 'LinkController@count');
Route::get('/link/moderation_log', 'LinkController@moderationLog');
Route::post('/link/moderation_log/data', 'LinkController@moderationLogData');

Route::get('/manual/count', 'ManualController@count');
Route::get('/manualSubdomain/count', 'ManualController@subdomainCount');
Route::post('/manual/update_action', 'ManualController@updateAction');
Route::get('/manual/found_log', 'ManualController@foundLog')->middleware('is_admin');
Route::post('/manual/found_log/data', 'ManualController@foundLogData')->middleware('is_admin');
Route::get('/manual/report', 'ManualController@report')->middleware('is_admin');

Route::get('/subdomain/statistic', 'SubdomainController@statistic');
Route::post('/subdomain/statistic/data', 'SubdomainController@data');

Route::post('/domain/back', 'DomainController@back')->name('domainBack');

$router->resource('domain', 'DomainController');
$router->resource('email', 'EmailController');
$router->resource('link', 'LinkController');
