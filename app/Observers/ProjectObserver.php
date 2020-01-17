<?php

namespace App\Observers;

use App\Models\Project;

class ProjectObserver
{

    /**
     * 监听数据即将创建的事件。
     *
     * @param  Project $project
     * @return void
     */
    public function creating(Project $project)
    {

    }

    /**
     * 监听数据创建后的事件。
     *
     * @param  Project $project
     * @return void
     */
    public function created(Project $project)
    {

    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param  Project $project
     * @return void
     */
    public function updating(Project $project)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param  Project $project
     * @return void
     */
    public function updated(Project $project)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param  Project $project
     * @return void
     */
    public function saving(Project $project)
    {

    }

    /**
     * 监听数据保存后的事件。
     *
     * @param  Project $project
     * @return void
     */
    public function saved(Project $project)
    {

    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param  Project $project
     * @return void
     */
    public function deleting(Project $project)
    {

    }

    /**
     * 监听数据删除后的事件。
     *
     * @param  Project $project
     * @return void
     */
    public function deleted(Project $project)
    {

    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param  Project $project
     * @return void
     */
    public function restoring(Project $project)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param  Project $project
     * @return void
     */
    public function restored(Project $project)
    {

    }
}