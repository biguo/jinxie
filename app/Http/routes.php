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

Route::get('/', function () {
    return view('welcome');
});

//接口路由
$api = app('Dingo\Api\Routing\Router');

// 将所有的 Exception 全部交给 App\Exceptions\Handler 来处理
app('api.exception')->register(function (Exception $exception) {
    $request = Illuminate\Http\Request::capture();
    return app('App\Exceptions\Handler')->render($request, $exception);
});


$api->version('v1', ['namespace' => 'App\Api\Controllers','middleware' => 'vue'], function ($api) {

    $api->any('banner/list', 'BannerController@getList');  //轮播图
    $api->any('article/list', 'ArticleController@getList');  //文章列表
    $api->any('article/search', 'ArticleController@search');  //文章搜索
    $api->any('article/{id}', 'ArticleController@show');  //文章搜索
    $api->any('center/list', 'ArticleController@getCenters');  //中心列表



});

