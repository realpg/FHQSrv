<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


//用户类路由
Route::group(['prefix' => '', 'middleware' => ['BeforeRequest']], function () {
    // 示例接口
    Route::get('test', 'API\TestController@test');

    //根据id获取用户信息
    Route::get('user/getById', 'API\UserController@getUserById');
    //根据id获取用户信息带token
    Route::get('user/getByIdWithToken', 'API\UserController@getUserInfoByIdWithToken')->middleware('CheckToken');
    //根据code获取openid（废弃）
    Route::get('user/getOpenIdForXCX', 'API\UserController@getOpenIdForXCX');
    //根据code获取openid
    Route::get('user/getXCXOpenId', 'API\UserController@getXCXOpenId');
    //登录/注册
    Route::post('user/login', 'API\UserController@login');
    //更新用户信息
    Route::post('user/updateById', 'API\UserController@updateUserById')->middleware('CheckToken');
    //解密encryptedData
    Route::post('user/encryptedData', 'API\UserController@encryptedData');

    //获取首页信息
    Route::get('home', 'API\UserController@getHome');

    //资讯信息
    Route::get('zx/list', 'API\ZXController@getZXList');
    Route::get('zx/getById', 'API\ZXController@getZXById');

    //服务信息
    Route::get('good/list', 'API\GoodController@getGoodList');
    Route::get('good/getById', 'API\GoodController@getGoodById');

    //企业信息
    Route::get('enter/getListByUserId', 'API\EnterController@getListByUserId')->middleware('CheckToken');
    Route::get('enter/getById', 'API\EnterController@getById')->middleware('CheckToken');
    Route::post('enter/del', 'API\EnterController@del')->middleware('CheckToken');
    Route::post('enter/edit', 'API\EnterController@edit')->middleware('CheckToken');

    //支付
    Route::post('wxpay/prepay', 'API\PayController@prepay');
    Route::post('wxpay/temPrepay', 'API\PayController@temPrepay')->middleware('CheckToken');
    Route::get('wxpay/getListByUserId', 'API\PayController@getListByUserId')->middleware('CheckToken');
    Route::get('wxpay/getListByEnterId', 'API\PayController@getListByEnterId')->middleware('CheckToken');
    Route::get('wxpay/getById', 'API\PayController@getById')->middleware('CheckToken');

});