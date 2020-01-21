<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\LittleTip;
use App\Http\Controllers\Controller;
use App\Models\CenterUser;
use App\Models\Project;
use App\Models\ProjectRecord;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ProjectControllers extends Controller
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

            $content->header('研究任务');
            $content->description('description');

            $content->body($this->grid());
            $content->body($this->grid('1'));
            $content->body($this->grid('2'));
            $content->body($this->grid('3'));
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
        return Admin::content(function (Content $content) use ($id) {

            $content->header('研究任务');
            $content->description('编辑');

            $form = $this->form();
            $form->builder()->addHiddenField((new Form\Field\Hidden(Form\Builder::PREVIOUS_URL_KEY))->value(URL::current()));  // 原地更新
            $content->row($form->edit($id));
            $content->row($this->recordGird($id));
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

            $content->header('任务');
            $content->description('description');

            $content->body($this->form());
        });
    }

    protected function recordGird($project_id)
    {
        return Admin::grid(ProjectRecord::class, function (Grid $grid) use ($project_id) {

            $grid->disableCreation();
            $grid->model()->where('project_id', $project_id)->orderBy('id', 'desc');
            $grid->tools(function (Grid\Tools $tools) {
                $tools->disableRefreshButton();
                $tools->append(new LittleTip('变动记录'));
            });
            $grid->disablePagination();
            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableActions();
            $grid->column('description', ' ')->display(function ($description) {
                return '由 '.$this->name.' 更新于 '.$this->updated_at.': &nbsp;&nbsp;' . $description;
            });
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid($scenario = '')
    {
        return Admin::grid(Project::class, function (Grid $grid) use ($scenario) {


            if ($scenario === '') {           #第一个
                $grid->model()->where('center_id', $this->center)->where('to', 0);
                $grid->tools(function (Grid\Tools $tools) {

                    $tools->disableRefreshButton();
                    $tools->append(new LittleTip('未分配的'));
                });
                $grid->model()->where('to', 0);
            } elseif ($scenario === '1') {
                $grid->disableCreation();
                $grid->model()->where('center_id', $this->center)->where('to', $this->mid);
                $grid->tools(function (Grid\Tools $tools) {
                    $tools->disableRefreshButton();
                    $tools->append(new LittleTip('指派给我的'));
                });
            } elseif ($scenario === '2') {
                $grid->disableCreation();
                $grid->model()->where('center_id', $this->center)->where('from', $this->mid);
                $grid->tools(function (Grid\Tools $tools) {
                    $tools->disableRefreshButton();
                    $tools->append(new LittleTip('我报告的'));
                });
            } elseif ($scenario === '3') {
                $grid->disableCreation();
                $grid->model()->where('center_id', $this->center)->where('from', $this->mid);
                $grid->tools(function (Grid\Tools $tools) {
                    $tools->disableRefreshButton();
                    $tools->append(new LittleTip('已完成的'));
                });
            }


            $grid->disablePagination();
            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableRowSelector();


            $grid->id('#');
            $grid->column('title', '主题')->display(function ($title) {
                return "<a href='" . admin_url('project/' . $this->id . '/edit') . "'>$title</a>";
            });
            $grid->column('priority', '优先级')->display(function ($priority) {
                $arr = ['0' => '无', '1' => '低', '2' => '中', '3' => '高', '4' => '紧急', '5' => '非常紧急'];
                return $arr[$priority];
            });
            $grid->column('to', '指派给')->display(function ($to) {
                return ($to === 0) ? '-' : Administrator::find($to)->name;
            });

            $grid->column('percent', '完成百分比')->progressBar1();
            $grid->updated_at('最后更新于');
            $grid->created_at('报告日期');
            $user = Auth::guard('admin')->user();

            $grid->actions(function (Grid\Displayers\Actions $actions) use ($user) {

                    if($actions->row->from === $user->id){

                    }elseif ($actions->row->to === $user->id){
                        $actions->disableDelete();
                    }else{
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
        return Admin::form(Project::class, function (Form $form) {

            $form->text('title')->rules('required');
            $form->textarea('description');

            $options = CenterUser::from('admin_center_users as r')
                ->leftJoin('admin_users as u', 'u.id', '=', 'r.user_id')
                ->where('r.center_id', $this->center)
                ->pluck('u.name', 'u.id')->toarray();
//            });
            $options = ['' => '请选择'] + $options;
            $form->select('to', '发送给')->options($options);

            $form->select('priority', '优先级')->options(['0' => '无', '1' => '低', '2' => '中', '3' => '高', '4' => '紧急', '5' => '非常紧急']);
            $form->select('category', '种类')->options(['0' => '-']);

            $form->date('begin_date', '开始日期')->rules('required');
            $form->date('end_date', '计划完成日期')->rules('required');

            $form->select('percent', '完成百分比')->options(['0' => '0%', '10' => '10%', '20' => '20%', '30' => '30%', '40' => '40%', '50' => '50%', '60' => '60%', '70' => '70%', '80' => '80%', '90' => '90%', '100' => '100%']);
            $form->hidden('from')->value($this->mid);
            $form->hidden('center_id')->value($this->center);
            $form->hidden('status')->value(0);
        });
    }
}

