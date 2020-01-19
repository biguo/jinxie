<?php

namespace App\Observers;

use App\Models\Center;
use App\Models\CenterUser;
use App\Models\Project;
use App\Models\ProjectRecord;
use Illuminate\Support\Facades\Auth;

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
        $original = array_except(Project::find($project->id)->toArray(), ['created_at', 'updated_at']);
        $original['begin_date'] = str_replace(' 00:00:00', '', $original['begin_date']);
        $original['end_date'] = str_replace(' 00:00:00', '', $original['end_date']);

        $attr['json'] = json_encode($original);

        $new = array_except($project->toArray(), ['created_at', 'updated_at']);
        $diff1 = array_diff($original, $new);
        $diff2 = array_diff($new, $original);

        if ($diff1 && $diff2) {
            $user = Auth::guard('admin')->user();
            $attr['mid'] = $user->id;
            $attr['name'] = $user->name;
            $cu = CenterUser::where('user_id', $user->id)->value('center_id');
            $attr['center_id'] = $cu ? $cu : Center::where('slug', GLOBAL_CENTER)->value('id');
            $attr['project_id'] = $project->id ;
            $str = '';
            $arr = ['priority' => '优先级','category' => '种类','to' => '发送给','begin_date' => '开始日期','end_date' => '计划完成日期','title' => '标题','description' => '描述','percent' => '完成百分比'];
            foreach ($diff1 as $key => $value) {
                $str .= $arr[$key] . '从' . $value . '变更为' . $diff2[$key] . '; ';
            }
            $attr['description'] = $str;
            ProjectRecord::create($attr);
        }
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