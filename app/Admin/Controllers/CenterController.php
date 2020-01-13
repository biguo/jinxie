<?php

namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\CustomerAdmin;
use App\Models\FileModel;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

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
     * createAdmin.
     *
     * @param $id
     * @return Content
     */
    public function createAdmin($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('设置中心管理员');
            $content->description('');
            $content->body($this->base($id));
        });
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setAdmin($id)  //仅仅利用一下验证
    {
        $params = Input::all();
        $tempForm = $this->base($id);
        if ($validationMessages = $tempForm->validationMessages1($params)) {
            return back()->withInput()->withErrors($validationMessages);
        }
        $file = $params['avatar'];  //Illuminate\Http\UploadedFile对象
        $attr = array_only($params, ['username', 'name', 'password']);
        $attr['password'] = bcrypt($attr['password']);
        $attr['avatar'] = str_replace(Upload_Domain, '', (new FileModel())->uploads($file->getPathname(), $file->getClientOriginalName()));

        DB::transaction(function () use ($attr, $id) {
            $user = CustomerAdmin::create($attr);
            $role_id = Role::where('slug', CENTER_ADMIN)->value('id');
            Center::saveCenterUser($id, $user->id, $role_id);
        });

        admin_toastr(trans('admin::lang.save_succeeded'));
        return redirect(admin_url('center'));

    }

    public function base($id)
    {
        return CustomerAdmin::baseForm(admin_url('setAdmin/' . $id));
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

            $grid->model()->from('center as c')
                ->leftJoin('admin_center_users as r', 'r.center_id', '=', 'c.id')
                ->leftJoin('admin_users as u', 'u.id', '=', 'r.user_id')
                ->select('c.id', 'c.title', 'c.status', 'c.slug', 'u.name as username');

//            $grid->disableCreation();
            $grid->disableFilter();
            $grid->disableRowSelector();
//            $grid->disableActions();
            $grid->column('id', 'ID');
            $grid->column('title', '项目名')->editable();
            $grid->column('username', '设置管理员')->display(function () {
                return $this->username ? $this->username : '暂无';
            });

            $grid->status()->switch();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                // append一个操作
                if ($actions->row->username === null) {
                    $actions->append('<a href="' . admin_url('createAdmin/' . $actions->getKey()) . '" title="设置管理员"><i class="fa fa-user"></i></a>');
                } else {
                    $actions->append('<a href="" title="更换管理员"><i class="fa fa-user"></i></a>');
                }

                if ($actions->row->slug === GLOBAL_CENTER) {
                    $actions->disableDelete();
                    $actions->disableEdit();
                }
            });

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
        });
    }

    /**
     * 用于面包屑
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function backList()
    {
        return redirect(admin_url('center'));
    }
}
