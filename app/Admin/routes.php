<?php

use Illuminate\Routing\Router;

Admin::registerHelpersRoutes();

Route::group([
    'prefix'        => config('admin.prefix'),
    'namespace'     => Admin::controllerNamespace(),
    'middleware'    => ['web', 'admin'],
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('banner', BannerController::class);
    $router->resource('center', CenterController::class);


    $router->any('createAdmin/{id}', 'CenterController@createAdmin');   //设置中心分中心管理员
    $router->any('setAdmin/{id}', 'CenterController@setAdmin');
    $router->any('createAdmin', 'CenterController@backList');  // 用于面包屑

    $router->any('chooseAdmin/{id}', 'CenterController@chooseAdmin');  // 更换中心/分中心管理员
    $router->any('chosenAdmin/{id}', 'CenterController@chosenAdmin');
    $router->any('chooseAdmin', 'CenterController@backList');  // 用于面包屑

    $router->resource('center_user', CenterUserController::class);
    $router->resource('files', FileController::class);
    $router->resource('project', ProjectControllers::class);
    $router->resource('article', ArticleController::class);
    $router->resource('category', CategoryController::class);


});
