<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::post('oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/help', function () {
    return view('help');
});

Route::get('/gallery/{id}', ['as' => 'show-Gallery', 'uses' => 'NewsController@getGallery']);
Route::get('/galleryadv/{id}', ['as' => 'show-GalleryAdv', 'uses' => 'NewsController@getGalleryAdv']);
Route::get('/news/stream', ['as' => 'get-newsstream', 'uses' => 'NewsController@getStream']);
Route::get('/supplier/stream', ['as' => 'get-supplierstream', 'uses' => 'SupplierController@getStream']);
Route::get('/coupon/stream', ['as' => 'get-couponstream', 'uses' => 'CouponController@getStream']);
Route::get('/event/stream', ['as' => 'get-eventstream', 'uses' => 'EventController@getStream']);

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
