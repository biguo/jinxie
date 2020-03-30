<?php

namespace App\Admin\Controllers;

use App\Models\File;

use App\Models\FileType;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
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

            $content->header('资料库');
            $content->description('description');

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

            $content->header('资料库');
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

            $content->header('资料库');
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
        return Admin::grid(File::class, function (Grid $grid) {
            $grid->model()->from('files as a')
                ->join('file_type as c', 'c.id', '=', 'a.type_id')
                ->where('mid', $this->mid)
                ->select('a.*', 'c.name as type');

            $grid->model()->where('mid', $this->mid)->orderBy('id', 'desc');
            $grid->id('ID')->sortable();
            $grid->column('title', '标题')->editable();
            $grid->path('路径')->display(function ($path) {
                return '<a href="'.Upload_Domain.$path.'" target="_blank">'.$path.'</a>';
            });
            $grid->column('type', 'category');
            $grid->created_at();
            $grid->updated_at();
            $grid->disableExport();
            $grid->filter(function ($filter) {
//                $filter->useModal();
                $filter->disableIdFilter();
                $filter->like('title', 'Search');
                $array = FileType::pluck('name', 'id')->toarray();
                $filter->is('type_id', '类型')->select($array);
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
        return Admin::form(File::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('title', 'title')->rules('required|min:3');
            $form->file('path', 'path')->rules('required');
            $array = FileType::pluck('name', 'id')->toarray();
            $form->select('type_id', '类型')->options(['' => '请选择'] + $array)->rules('required');
            $form->hidden('center_id' )->default($this->center);
            $form->hidden('mid')->default($this->mid);
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
