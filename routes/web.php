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

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/conversation/{group}/getListChat', 'ConversationController@getListChat')->name('getListChat');
Route::get('/conversation/{group}/getListConversation', 'ConversationController@getListConversation')->name('getListConversation');
Route::post('/conversation/{group}/change-status', 'ConversationController@changeStatus')->name('changeStatus');
Route::get('/conversation/{group}/later/{conversation}/{id}', 'ConversationController@getLastest')->name('getLastest');
Route::post('/conversation/active', 'ConversationController@active')->name('active');
Route::post('/group/{group}/done', 'GroupController@postDone');
Route::post('/group/{group}/import', 'GroupController@import');
Route::post('/group/{group}/addConversation', 'ConversationController@addConversation');
Route::resource('users', 'UserController');
Route::resource('groups', 'GroupController');
Route::resource('conversations', 'ConversationController');
Route::get('/test', 'TestController@test')->name('test');
Route::get('/exportWord', 'GroupController@exportWord')->name('exportWord');
