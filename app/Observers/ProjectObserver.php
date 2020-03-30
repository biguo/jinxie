<?php

namespace App\Observers;

use App\Models\Center;
use App\Models\CenterUser;
use App\Models\Project;
use App\Models\ProjectRecord;
use Encore\Admin\Auth\Database\Administrator;
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

        $original = array_except($project->toArray(), ['created_at', 'updated_at']);
        $attr['json'] = json_encode($original);
        //Array ( [title] => 张君雅1 [description] => 张君雅12 [to] => [priority] => 0 [category] => 0 [begin_date] => 2020-01-19
        // [end_date] => 2020-01-20 [percent] => 0 [from] => 101 [center_id] => 6 [status] => 0 [id] => 6 )
        $user = Auth::guard('admin')->user();
        $attr['mid'] = $user->id;
        $attr['name'] = $user->name;
        $cu = CenterUser::where('user_id', $user->id)->value('center_id');
        $attr['center_id'] = $cu ? $cu : Center::where('slug', GLOBAL_CENTER)->value('id');
        $attr['project_id'] = $project->id;
        $attr['description'] = '创建了项目:"' . $original['title'] . '"';
        ProjectRecord::create($attr);

        if ($original['to'] !== '') {
            $toAttr = $attr;
            $new = Administrator::find($original['to'])->name;
            $str = '将任务指派给了' . $new . '; ';
            $toAttr['description'] = $str;
            ProjectRecord::create($toAttr);
            $project->status = 1;
            $project->save();
        }
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

        $diff1 = array_diff_assoc($original, $new);
        $diff2 = array_diff_assoc($new, $original);

        if ($diff1 || $diff2) {
            $user = Auth::guard('admin')->user();
            $attr['mid'] = $user->id;
            $attr['name'] = $user->name;
            $cu = CenterUser::where('user_id', $user->id)->value('center_id');
            $attr['center_id'] = $cu ? $cu : Center::where('slug', GLOBAL_CENTER)->value('id');
            $attr['project_id'] = $project->id;
            $str = '';
            $arr = ['priority' => '优先级', 'category' => '种类', 'to' => '处理人', 'begin_date' => '开始日期', 'end_date' => '计划完成日期', 'title' => '标题', 'description' => '描述', 'percent' => '完成百分比'];
            $priorityArr = ['0' => '无', '1' => '低', '2' => '中', '3' => '高', '4' => '紧急', '5' => '非常紧急'];

            $diff1_to = array_only($diff1, ['to']);
            $diff2_to = array_only($diff2, ['to']);
            $status = 0;


            if ($diff1_to || $diff2_to) {

                $new = Administrator::find($diff2_to['to'])->name;

                if ((!isset($diff1_to['to'])) || ($diff1_to['to'] === '')) {
                    $str .= '将任务指派给了' . $new . '; ';
                    $status = 1;
                } else {
                    $old = Administrator::find($diff1_to['to'])->name;
                    $str .= '处理人从' . $old . '变更为' . $new . '; ';
                }
                $toAttr = $attr;
                $toAttr['description'] = $str;
                ProjectRecord::create($toAttr);
            }

            $diff1_ = array_except($diff1, ['to','status']);
            $diff2_ = array_except($diff2, ['to','status']);


            if ($diff1_ || $diff2_) {
                foreach ($diff2_ as $key => $value) {
                    $old = $diff1_[$key];
                    $new = $value;

                    if ($key === 'priority') {
                        $old = $priorityArr[$old];
                        $new = $priorityArr[$new];
                    }
                    if (($key === 'percent') && ($new === '100')) {
                        $status = 2;
                    }
                    $str .= $arr[$key] . '从' . $old . '变更为' . $new . '; ';

                }
                $attr['description'] = $str;
                ProjectRecord::create($attr);

            }

            if ($status !== 0) {
                $project->status = $status;
            }
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
        ProjectRecord::where('project_id', $project->id)->delete();
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