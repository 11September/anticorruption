<?php

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Route::get('/', 'ObjectsController@index')->name('welcome');
Route::post('/filter', 'ObjectsController@filter')->name('filter');

//Dynamic Routes
Route::get('/page-{slug}', 'PagesController@page');
//Dynamic Routes

Route::get('/get-object', 'ObjectsController@show');
Route::get('/get-object-facebook/{id}', 'ObjectsController@facebook');

Route::post('/export-download', 'PagesController@exportDownload');
Route::get('/additional-download/{path}', 'PagesController@addtionalDownload');

/* Middleware Admin */
Route::post('/importObjectsDatabase', 'ObjectsController@importObjectsDatabase')->name('importObjectsDatabase')->middleware('auth');
Route::get('/export-download/admin', 'ObjectsController@exportDownloadAdmin')->middleware('auth', 'isAdmin');

//admin ajax delete

Route::post('/remove_document', 'DocumentsController@delete')->middleware('auth', 'isAdmin');
Route::post('/remove_finances', 'FinancesController@delete')->middleware('auth' , 'isAdmin');
//admin ajax delete

/* Middleware Admin end */



//delete after
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/redirect/{provider}/{objectId}', 'SocialAuthController@redirect');
Route::get('/callback/{provider}', 'SocialAuthController@callback');

Route::post('/remove_comment', 'CommentsController@delete')->middleware('auth', 'isAdmin');

//Route::get('/api', 'PagesController@api')->name('api');
//Route::get('/instruction', 'PagesController@instruction')->name('instruction');