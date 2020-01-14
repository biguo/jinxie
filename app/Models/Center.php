<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class Center extends Model
{
    protected $table = 'center';

    public $timestamps = false;

    /**
     * 研究员设置
     * @param $center_id
     * @param $user_id
     * @param $role_id
     */
    public static function saveCenterUser($center_id, $user_id, $role_id)
    {
        CenterUser::where(['center_id' => $center_id, 'user_id' => $user_id])->delete();
        CenterUser::insert(['center_id' => $center_id, 'user_id' => $user_id, 'role_id' => $role_id] );
        RoleUser::where(['user_id' => $user_id])->delete();
        RoleUser::insert(['role_id' => $role_id, 'user_id' => $user_id]);


    }

    /**
     * 管理员设置
     * @param $center_id
     * @param $user_id
     * @param $role_id
     */
    public static function saveCenterManagr($center_id, $user_id, $role_id)
    {
        $centerRoleId = Role::where('slug', CENTER_ADMIN)->value('id');
        $branchRoleId = Role::where('slug', BRANCH_ADMIN)->value('id');
        CenterUser::where(['center_id' => $center_id, 'role_id' => $centerRoleId])->delete();
        CenterUser::where(['center_id' => $center_id, 'role_id' => $branchRoleId])->delete();
        CenterUser::insert(['center_id' => $center_id, 'user_id' => $user_id, 'role_id' => $role_id]);
        RoleUser::where(['user_id' => $user_id])->delete();
        RoleUser::insert(['role_id' => $role_id, 'user_id' => $user_id]);
    }
}
