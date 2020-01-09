<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class Center extends Model
{
    protected $table = 'center';

    public $timestamps = false;

    public static function saveCenterUser($center_id, $user_id, $role_id)
    {
        CenterUser::where(['center_id' => $center_id, 'user_id' => $user_id])->delete();
        CenterUser::insert(['center_id' => $center_id, 'user_id' => $user_id, 'role_id' => $role_id] );
        $attr1 = ['role_id' => $role_id, 'user_id' => $user_id];
        RoleUser::where($attr1)->delete();
        RoleUser::insert($attr1);
    }

}
