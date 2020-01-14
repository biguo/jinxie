<?php

namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\CenterUser;
use App\Models\CustomerAdmin;
use App\Models\FileModel;
use App\Models\RoleUser;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\Auth;
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
            $centerRoleId = Role::where('slug', CENTER_ADMIN)->value('id');
            $branchRoleId = Role::where('slug', BRANCH_ADMIN)->value('id');
            $grid->model()->from('center as c')
                ->leftJoin('admin_center_users as r', 'r.center_id', '=', 'c.id')
                ->leftJoin('admin_users as u', 'u.id', '=', 'r.user_id')
                ->select('c.id', 'c.title', 'c.status', 'c.slug', 'u.name as username')
                ->whereIn('r.role_id',[$centerRoleId,$branchRoleId ]);

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
            $isAdmin = Auth::guard('admin')->user()->isAdministrator();
            $grid->actions(function (Grid\Displayers\Actions $actions) use ($isAdmin) {
                if ($actions->row->slug === GLOBAL_CENTER) {
                    $actions->disableDelete();
                    $actions->disableEdit();
                    if ($isAdmin) {
                        $actions->append('<div class="btn-group left" style="margin-right: 10px"><a href="' . admin_url('createAdmin/' . $actions->getKey()) . '" class="btn btn-sm btn-success"><i class="fa fa-safari"></i>&nbsp;&nbsp;设置管理员</a></div>');
                        $actions->append('<div class="btn-group left" style="margin-right: 10px"><a href="' . admin_url('chooseAdmin/' . $actions->getKey()) . '" class="btn btn-sm btn-primary"><i class="fa fa-user"></i>&nbsp;&nbsp;更换管理员</a></div>');
                    }
                } else {
                    $actions->append('<div class="btn-group left" style="margin-right: 10px"><a href="' . admin_url('createAdmin/' . $actions->getKey()) . '" class="btn btn-sm btn-success"><i class="fa fa-safari"></i>&nbsp;&nbsp;设置管理员</a></div>');
                    $actions->append('<div class="btn-group left" style="margin-right: 10px"><a href="' . admin_url('chooseAdmin/' . $actions->getKey()) . '" class="btn btn-sm btn-primary"><i class="fa fa-user"></i>&nbsp;&nbsp;更换管理员</a></div>');
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
     * chooseAdmin.
     *
     * @param $id
     * @return Content
     */
    public function chooseAdmin($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('更换中心管理员');
            $content->description('');
            $content->body($this->selectAdmin($id));
        });
    }

    /**
     * 选择管理员
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function chosenAdmin($id)
    {
        $params = Input::all();
        $attr = array_only($params, ['user_id', 'role_id']);
        if (!empty($attr)) {
            DB::transaction(function () use ($attr, $id) {
                Center::saveCenterManagr($id, $attr['user_id'], $attr['role_id']);
            });
            admin_toastr(trans('admin::lang.save_succeeded'));
        }
        return redirect(admin_url('center'));
    }


    /**
     * 用于选择中心的管理员的Form
     * @param $id
     * @return mixed
     */
    public function selectAdmin($id)
    {
        return Admin::form(CenterUser::class, function (Form $form) use ($id) {
            $center = Center::find($id);
            $centerRoleId = Role::where('slug', CENTER_ADMIN)->value('id');
            $branchRoleId = Role::where('slug', BRANCH_ADMIN)->value('id');
            $roleId = ($center->slug === GLOBAL_CENTER) ? $centerRoleId : $branchRoleId;
            $centerUser = CenterUser::from('admin_center_users as cu') ->leftJoin('admin_users as u', 'u.id', '=', 'cu.user_id')
                ->where([['cu.center_id', $id], ['cu.role_id', $roleId]])->pluck('u.name', 'u.id')->toarray();
            $default = $centerUser ? $centerUser : [];
            $array = Administrator::from('admin_users as u')
                ->leftJoin('admin_role_users as ru', 'u.id', '=', 'ru.user_id')
                ->leftJoin('admin_center_users as cu', 'cu.user_id', '=', 'u.id')
                ->whereIn('ru.role_id', [$centerRoleId, $branchRoleId])
                ->where('cu.center_id', null)
                ->pluck('u.name', 'u.id')->toarray();
            $array = $default + $array;

            $form->select('user_id', '选择管理员')->options($array);
            $form->hidden('role_id')->default($roleId);
            $form->setAction(admin_url('chosenAdmin/' . $id));
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
     * 设置管理员
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setAdmin($id)  //仅仅利用一下验证
    {
        $params = Input::all();
        if (!empty($params)) {
            $file = $params['avatar'];  //Illuminate\Http\UploadedFile对象
            $attr = array_only($params, ['username', 'name', 'password']);
            $tempForm = $this->base($id);
            if ($validationMessages = $tempForm->validationMessages1($params)) {
                return back()->withInput()->withErrors($validationMessages);
            }
            $attr['password'] = bcrypt($attr['password']);
            $attr['avatar'] = str_replace(Upload_Domain, '', (new FileModel())->uploads($file->getPathname(), $file->getClientOriginalName()));

            DB::transaction(function () use ($attr, $id) {
                $user = CustomerAdmin::create($attr);
                $center = Center::find($id);
                $centerRoleId = Role::where('slug', CENTER_ADMIN)->value('id');
                $branchRoleId = Role::where('slug', BRANCH_ADMIN)->value('id');
                $roleId = ($center->slug === GLOBAL_CENTER) ? $centerRoleId : $branchRoleId;
                Center::saveCenterManagr($id,$user->id,$roleId);

            });
            admin_toastr(trans('admin::lang.save_succeeded'));
        }
        return redirect(admin_url('center'));

    }

    /**
     * 用于设置中心的管理员的Form
     * @param $id
     * @return Form
     */
    public function base($id)
    {
        return CustomerAdmin::baseForm(admin_url('setAdmin/' . $id));
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
