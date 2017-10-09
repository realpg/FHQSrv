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


//登录

Route::get('/admin/login', 'Admin\LoginController@login');        //登录
Route::post('/admin/login', 'Admin\LoginController@loginPost');   //post登录请求
Route::get('/admin/loginout', 'Admin\LoginController@loginout');  //注销

Route::group(['prefix' => 'admin', 'middleware' => ['admin.login']], function () {

    //首页
    Route::get('/', 'Admin\IndexController@index');       //首页
    Route::get('/index', 'Admin\IndexController@index');  //首页
    Route::get('/dashboard/index', 'Admin\IndexController@index');    //首页

    //错误页面
    Route::get('/error/500', 'Admin\IndexController@error');  //错误页面

    //广告管理
    Route::get('/ad/index', 'Admin\ADController@index');  //广告管理首页
    Route::get('/ad/setStatus/{id}', 'Admin\ADController@setStatus');  //设置广告状态
    Route::get('/ad/del/{id}', 'Admin\ADController@del');  //删除广告
    Route::get('/ad/edit', 'Admin\ADController@edit');  //新建或编辑广告
    Route::post('/ad/edit', 'Admin\ADController@editPost');  //新建或编辑广告

    //管理员管理
    Route::get('/admin/index', 'Admin\AdminController@index');  //管理员管理首页
    Route::get('/admin/del/{id}', 'Admin\AdminController@del');  //删除管理员
    Route::get('/admin/edit', 'Admin\AdminController@edit');  //新建或编辑管理员
    Route::post('/admin/edit', 'Admin\AdminController@editPost');  //新建或编辑管理员

    //资讯管理
    Route::get('/zx/index', 'Admin\ZXController@index');  //资讯管理首页
    Route::get('/zx/setStatus/{id}', 'Admin\ZXController@setStatus');  //设置资讯状态
    Route::get('/zx/del/{id}', 'Admin\ZXController@del');  //删除资讯
    Route::get('/zx/edit', 'Admin\ZXController@edit');  //新建或编辑资讯
    Route::post('/zx/edit', 'Admin\ZXController@editPost');  //新建或编辑资讯

    //服务管理
    Route::get('/good/index', 'Admin\GoodController@index');  //服务管理首页
    Route::get('/good/setStatus/{id}', 'Admin\GoodController@setStatus');  //设置服务状态
    Route::get('/good/del/{id}', 'Admin\GoodController@del');  //删除服务
    Route::get('/good/edit', 'Admin\GoodController@edit');  //新建或编辑服务
    Route::post('/good/edit', 'Admin\GoodController@editPost');  //新建或编辑服务

    //图文管理
    Route::get('/tw/del/{id}', 'Admin\TWStepController@del');  //删除图文
    Route::get('/tw/edit', 'Admin\TWStepController@edit');  //图文编辑页面
    Route::post('/tw/edit', 'Admin\TWStepController@editPost');  //图文编辑页面

});