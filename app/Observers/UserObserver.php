<?php

namespace App\Observers;

use App\Models\CenterUser;
use App\Models\RoleUser;
use Encore\Admin\Auth\Database\Administrator;


class UserObserver
{

    /**
     * 监听数据即将创建的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function creating(Administrator $user)
    {

    }

    /**
     * 监听数据创建后的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function created(Administrator $user)
    {

    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function updating(Administrator $user)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function updated(Administrator $user)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function saving(Administrator $user)
    {

    }

    /**
     * 监听数据保存后的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function saved(Administrator $user)
    {

    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function deleting(Administrator $user)
    {

    }

    /**
     * 监听数据删除后的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function deleted(Administrator $user)
    {
        $attr = ['user_id' => $user->id];
        CenterUser::where($attr)->delete();
        RoleUser::where($attr)->delete();
    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function restoring(Administrator $user)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function restored(Administrator $user)
    {

    }
}