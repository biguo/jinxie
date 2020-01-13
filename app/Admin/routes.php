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


    $router->any('createAdmin/{id}', 'CenterController@createAdmin');
    $router->any('setAdmin/{id}', 'CenterController@setAdmin');
    $router->any('createAdmin', 'CenterController@backList');  // 用于面包屑

//    $router->any('/center/createAdmin/{id}', 'CenterController@createAdmin');
//    $router->any('/center/createAdmin/{id}', 'CenterController@createAdmin');


    $router->resource('center_user', CenterUserController::class);


});
