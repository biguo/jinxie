<?php

namespace App\Admin\Controllers;

use App\Models\Center;
use App\Models\CenterUser;
use App\Models\RoleUser;
use App\Models\UserPermission;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\MessageBag;


/**
 * 中心管理
 * Class UserController
 * @package App\Admin\Controllers
 */
class CenterUserController extends Controller
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
            $title = Center::find($this->center) ? Center::find($this->center)->title : '' ;
            $content->header(trans($title.' &nbsp;&nbsp;&nbsp; 人员管理界面'));
            $content->description(trans('admin::lang.list'));
            $content->body($this->grid()->render());
        });
    }

    public function destroy($id)
    {
        $attr = ['user_id' => $id];
        CenterUser::where($attr)->delete();
        RoleUser::where($attr)->delete();

        if ($this->form()->destroy($id)) {
            return response()->json([
                'status'  => true,
                'message' => trans('admin::lang.delete_succeeded'),
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => trans('admin::lang.delete_failed'),
            ]);
        }
    }


    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header(trans('admin::lang.administrator'));
            $content->description(trans('admin::lang.edit'));
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
            $content->header(trans('admin::lang.administrator'));
            $content->description(trans('admin::lang.create'));
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
        return Administrator::grid(function (Grid $grid) {

            $grid->model()->from('admin_users as u')->join('admin_center_users as r', 'r.user_id', '=', 'u.id')
                ->where('r.center_id', $this->center)->select('u.username', 'u.name', 'u.id')->orderBy('u.id', 'desc');
            $grid->id('ID')->sortable();
            $grid->username(trans('admin::lang.username'));
            $grid->name(trans('admin::lang.name'));
            $grid->roles(trans('admin::lang.roles'))->pluck('name')->label();

//            $grid->model()->buildData();  //获得字段
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                if (Administrator::find($actions->getKey())->isRole('center-admin')) {
                    $actions->disableDelete();
                    $actions->disableEdit();
                }
            });
            $grid->option('useRowSelector', false);
            $grid->tools(function (Grid\Tools $tools) {
                $tools->batch(function (Grid\Tools\BatchActions $actions) {
                    $actions->disableDelete();
                });
            });

            $grid->disableExport();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        return Administrator::form(function (Form $form) {
            $form->display('id', 'ID');

            $form->text('username', trans('admin::lang.username'))->rules('required');
            $form->text('name', trans('admin::lang.name'))->rules('required');
            $form->image('avatar', trans('admin::lang.avatar'));
            $form->password('password', trans('admin::lang.password'))->rules('required|confirmed');
            $form->password('password_confirmation', trans('admin::lang.password_confirmation'))->rules('required')
                ->default(function ($form) {
                    return $form->model()->password;
                });

            $form->ignore(['password_confirmation']);

            $form->saving(function (Form $form) {
                if ($form->password && $form->model()->password != $form->password) {
                    $form->password = bcrypt($form->password);
                }
            });

            $Researcher = Role::where('slug', 'Researcher')->value('id');
            $form->saved(function (Form $form) use ($Researcher) {
                Center::saveCenterUser($this->center, $form->model()->getKey(),$Researcher);
            });


        });
    }
}
