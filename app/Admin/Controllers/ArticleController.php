<?php

namespace App\Admin\Controllers;

use App\Models\Article;

use App\Models\Category;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ArticleController extends Controller
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

            $content->header('文章');
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

            $content->header('文章');
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

            $content->header('文章');
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
        return Admin::grid(Article::class, function (Grid $grid) {
            $grid->model()->from('article as a')
                ->join('category as c', 'c.id', '=', 'a.type_id')
                ->select('a.*','c.name');

            $grid->id('ID')->sortable();
            $grid->title()->editable();
            $grid->content()->editable();
            $grid->name();
            $grid->image()->image(Upload_Domain, 100, 100);
            $grid->status()->switch();
            $grid->sort()->editable();
            $grid->created_at();
            $grid->updated_at();
            $grid->filter(function ($filter) {
//                $filter->useModal();
                $filter->disableIdFilter();
                $filter->like('title', 'Search');
                $filter->is('status', '状态')->select([
                    '1' => '已上线',
                    '0' => '已撤销',
                ]);
                $array = Category::pluck('name', 'id')->toarray();

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
        return Admin::form(Article::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('title', 'title')->rules('required|min:3');
            $form->ckeditor('content', 'content');
            $form->image('image', 'image');
            $array = Category::pluck('name', 'id')->toarray();

            $form->select('type_id', '类型')->options(['' => '请选择'] + $array)->rules('required');
            $form->hidden('center_id')->value($this->center);
            $form->hidden('mid')->value($this->mid);
            $form->hidden('sort');
            $form->hidden('status');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
