<?php

namespace App\Observers;

use App\Models\File;
use App\Models\FileType;
use Encore\Admin\Auth\Database\Administrator;


class FileTypeObserver
{

    /**
     * 监听数据即将创建的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function creating(FileType $type)
    {

    }

    /**
     * 监听数据创建后的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function created(FileType $type)
    {

    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function updating(FileType $type)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function updated(FileType $type)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function saving(FileType $type)
    {

    }

    /**
     * 监听数据保存后的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function saved(FileType $type)
    {

    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function deleting(FileType $type)
    {

    }

    /**
     * 监听数据删除后的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function deleted(FileType $type)
    {
        $attr = ['type_id' => $type->id];
        File::where($attr)->delete();
    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function restoring(FileType $type)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param  Administrator $user
     * @return void
     */
    public function restored(FileType $type)
    {

    }
}