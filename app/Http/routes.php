<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use App\Person;


    Route::get('/', function () {
        if (Auth::check()) {
            return redirect('admin/person');
        }
        return view('auth.login');
    });

    Route::get('/reset/password/{token}', 'Auth\AuthController@resetpassword');
    Route::post('/postReset', 'Auth\AuthController@postReset');
    Route::post('/password/emailsend', 'Auth\AuthController@emailsend');


    Route::auth();
    Route::get('/home', 'HomeController@index');

Route::group(['middleware' => 'auth'], function () {
    Route::post('/admin/person/store', 'Admin\PersonController@store');
    Route::post('/admin/entity/store', 'Admin\EntityController@store');
    Route::get('admin/person/view', 'Admin\PersonController@view');
    Route::get('admin/entity/view', 'Admin\EntityController@view');
    Route::post('/admin/person/update/{id}', 'Admin\PersonController@update');
    Route::post('/admin/entity/update/{id}', 'Admin\EntityController@update');
    Route::post('/admin/entity/validatetmo', 'Admin\EntityController@validatetmo');

    Route::post('/admin/person/changeStatus/{id}', 'Admin\PersonController@changeStatus');
    Route::post('/admin/entity/changeStatus/{id}', 'Admin\EntityController@changeStatus');
    Route::get('/admin/entity/pdf-export/{params?}', 'Admin\EntityController@generate_pdf');
    Route::get('/admin/entity/xls-export/{params?}', 'Admin\EntityController@generate_xls');

    Route::get('/admin/person/pdf-export/{params?}', 'Admin\PersonController@generate_pdf');
    Route::get('/admin/person/xls-export/{params?}', 'Admin\PersonController@generate_xls');
    Route::post('/apmin/person/forget', 'Admin\PersonController@forget');
    Route::resource('demo', 'DemoController');
    Route::resource('admin/logs', 'Admin\LogsController');


    Route::group(['middlewareGroups' => ['web']], function () {

        Route::post('/admin/person/set', 'Admin\PersonController@setids');

        Route::resource('admin/person', 'Admin\PersonController');
        Route::resource('admin/entity', 'Admin\EntityController');
//        Route::get('/admin/person/xls-export', 'Admin\PersonController@_generate_xls');
        Route::delete('/admin/entity/destroy_entity/{id}',array('uses' => 'Admin\EntityController@destroy_entity'));


    });


});


