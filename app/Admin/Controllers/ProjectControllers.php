<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CenterUser;
use App\Models\Project;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

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
            $content->body($this->grid());
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

            $content->header('任务');
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
        return Admin::grid(Project::class, function (Grid $grid) {
            $grid->disableCreation();
            $grid->disablePagination();
            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->tools(function (Grid\Tools $tools) {
                $tools->disableRefreshButton();
            });

            $grid->id('#');
            $grid->column('title','主题')->display(function ($title) {
                return "<a href='".admin_url('project/' . $this->id.'/edit')."'>$title</a>";
            });
            $grid->column('priority','优先级')->display(function ($priority) {
                $arr = ['0' => '无', '1' => '低', '2' => '中', '3' => '高', '4' => '紧急', '5' => '非常紧急'];
                return $arr[$priority];
            });
            $grid->column('percent', '完成百分比')->progressBar1();
            $grid->updated_at('最后更新于');
            $grid->created_at('报告日期');
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

            $form->display('id', 'ID');
            $form->text('title')->rules('required');
            $form->ckeditor('description')->rules('required');

            $options = CenterUser::from('admin_center_users as r')
                ->leftJoin('admin_users as u', 'u.id', '=', 'r.user_id')
                ->where('r.center_id', $this->center)
                ->pluck('u.name', 'u.id')->toarray();
//            });
            $options = ['' => '请选择'] + $options;
            $form->select('to', '发送给')->options($options);

            $form->select('priority', '优先级')->options(['0' => '无', '1' => '低', '2' => '中', '3' => '高', '4' => '紧急', '5' => '非常紧急']);
            $form->select('category', '种类')->options(['0' => '-']);

            $form->date('begin_date', '开始日期');
            $form->date('end_date', '计划完成日期');

            $form->select('percent', '完成百分比')->options(['0' => '0%', '10' => '10%', '20' => '20%', '30' => '30%', '40' => '40%', '50' => '50%', '6' => '60%', '7' => '70%', '8' => '80%', '9' => '90%', '10' => '100%']);
            $form->hidden('from')->value($this->mid);
            $form->hidden('status')->value(0);
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
