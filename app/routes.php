<?php

/*
|--------------------------------------------------------------------------
| Home Page Routes
|--------------------------------------------------------------------------
|
| Home Page
|
*/

Route::get('/', function()
{
	return View::make('index'); //return app/views/index.php
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Prefixed with API
|
*/
Route::group(array('prefix' =>'api'), function(){
	//we have only index, store and destroy routes, user cannot create or edit
	//Angular will handle those
	Route::resource('sents', 'SentController',
		array('only'=>array('index','store', 'destroy')));
});

/*
|--------------------------------------------------------------------------
| Our Catch All Route
|--------------------------------------------------------------------------
|
| Anything not home or api redirected to frontend
|
*/

App::missing(function($exception){
	return View::make('index');
});