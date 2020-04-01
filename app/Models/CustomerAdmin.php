<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Form;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Self_;

class CustomerAdmin extends Administrator
{

    public function visible($roles)
    {
        if (empty($roles)) {
            return true;
        }

        $roles = array_column($roles, 'slug');

        if ($this->inRoles($roles)) {
            return true;
        }

        return false;
    }

    /**
     * @param \Closure $callback
     *
     * @return Form
     */
    public static function form(\Closure $callback)  //使用自定义form表单  为了使用自定义的验证
    {
        Form::registerBuiltinFields();

        return new CustomerForm(new static(), $callback);
    }



    public static function baseForm($actionUrl)
    {
        return self::form(function (Form $form) use ($actionUrl)  {
            $form->text('username', trans('admin::lang.username'))->rules('required');
            $form->text('name', trans('admin::lang.name'))->rules('required');
            $form->image('avatar', trans('admin::lang.avatar'))->rules('required');
            $form->password('password', trans('admin::lang.password'))->rules('required|confirmed');
            $form->password('password_confirmation', trans('admin::lang.password_confirmation'))->rules('required|min:6|same:password');
            $form->ignore(['password_confirmation']);
            $form->setAction($actionUrl);
        });
    }
}
