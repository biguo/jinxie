<?php

namespace App\Admin\Controllers;


use App\Admin\Extensions\CheckRow;
use App\Models\Center;
use App\Models\CenterUser;
use App\Models\RoleUser;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;

class CenterController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {

        return Admin::content(function (Content $content) {
            $content->header('分中心设置');
            $content->description('分中心设置');
            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {

//        return view('banner.edit',[]);

        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Center::class, function (Grid $grid) {

//            $grid->model()->from('center as c')->leftJoin('admin_users as u', 'u.id', '=', 'c.admin_user_id')
//                ->select('c.id', 'c.title', 'c.status', 'u.name as username');

//            $grid->disableCreation();
            $grid->disableFilter();
            $grid->disableRowSelector();
//            $grid->disableActions();
            $grid->column('id', 'ID');
            $grid->column('title', '项目名')->editable();
//            $grid->column('username', '设置管理员');

            $grid->status()->switch();

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Center::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('slug', 'slug')->rules('required|min:3');
            $form->text('title', 'title')->rules('required|min:3');
            $form->hidden('status')->default(0);


            $administrator = Role::where('slug', 'administrator')->value('id');
            $centerAdmin = Role::where('slug', 'center-admin')->value('id');

            $array = Administrator::from('admin_users as a')->leftJoin('admin_role_users as r', 'a.id', '=', 'r.user_id')
                ->whereRaw('(r.role_id != ? ) or (r.role_id is null)', [$administrator])->pluck('a.name', 'a.id')->toarray();


            $form->select('admin_user_id')->options(function () use ($array) {
                return ['0' => '请选择'] + $array;
            })->default(function ($form) use ($centerAdmin) {
                $user_id = CenterUser::where(['center_id' => $form->model()->getKey(), 'role_id' => $centerAdmin])->value('user_id');
                return $user_id;
            });

            $form->ignore(['admin_user_id']);

            $form->saved(function (Form $form) use ($centerAdmin) {
                $data = Input::all();
                Center::saveCenterUser($form->model()->getKey(),$data['admin_user_id'],$centerAdmin);
            });
        });
    }
}
