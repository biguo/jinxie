<?php

use Illuminate\Routing\Router;

Admin::registerHelpersRoutes();

//Route::get('admin/register', 'App\Admin\Controllers\AuthController@getRegister');
//Route::post('admin/register', 'App\Admin\Controllers\AuthController@postRegister');

Route::group([
    'prefix' => config('admin.prefix'),
    'namespace' => Admin::controllerNamespace(),
    'middleware' => ['web']
], function (Router $router) {
    $router->get('register', 'AuthController@getRegister');
    $router->post('register', 'AuthController@postRegister');
});

Route::group([
    'prefix' => config('admin.prefix'),
    'namespace' => Admin::controllerNamespace(),
    'middleware' => ['web', 'admin'],
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('banner', BannerController::class);
    $router->resource('center', CenterController::class);

    $router->resource('auth/users', 'UserController');
    $router->get('auth/login', 'AuthController@getLogin');
    $router->post('auth/login', 'AuthController@postLogin');
    $router->get('auth/logout', 'AuthController@getLogout');
    $router->get('auth/setting', 'AuthController@getSetting');
    $router->put('auth/setting', 'AuthController@putSetting');

    $router->any('createAdmin/{id}', 'CenterController@createAdmin');   //设置中心分中心管理员
    $router->any('setAdmin/{id}', 'CenterController@setAdmin');
    $router->any('createAdmin', 'CenterController@backList');  // 用于面包屑

    $router->any('chooseAdmin/{id}', 'CenterController@chooseAdmin');  // 更换中心/分中心管理员
    $router->any('chosenAdmin/{id}', 'CenterController@chosenAdmin');
    $router->any('chooseAdmin', 'CenterController@backList');  // 用于面包屑

    $router->resource('center_user', CenterUserController::class);
    $router->resource('files', FileController::class);          //附件
    $router->resource('project', ProjectControllers::class);
    $router->resource('article', ArticleController::class);
    $router->resource('category', CategoryController::class); //文章自定义类型
    $router->resource('types', FileTypeController::class); //附件自定义类型

    $router->any('mail/sended', 'MailController@sended');  // 发件箱
    $router->any('mail/{id}/softDelete', 'MailController@softDelete');  //软删
    $router->any('mail/{id}/show', 'MailController@show');  //查看
    $router->any('mail/{id}/reply', 'MailController@reply');  //回信
    $router->resource('mail', MailController::class); //站内邮件 收件箱

});
