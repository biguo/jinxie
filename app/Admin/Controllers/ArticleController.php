<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Center;
use App\Models\File;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

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
            $params = Input::all();
            if(isset($params['type_id'])){
                $category = Category::find($params['type_id']);
            }
            $content->header(isset($category)? $category->name : '文章列表');
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
            if(Admin::user()->inRoles([CENTER_ADMIN, SUPER])){  #超管和中心管理可以替分管写文章
                $grid->model()->from('article as a')
                    ->join('category as c', 'c.id', '=', 'a.type_id')
                    ->join('center as ce', 'ce.id', '=', 'a.center_id')
                    ->select('a.title as at','a.image', 'a.status', 'a.sort','a.id','c.name', 'ce.center_name as ct');
            }else{
                $grid->model()->from('article as a')
                    ->join('category as c', 'c.id', '=', 'a.type_id')
                    ->join('center as ce', 'ce.id', '=', 'a.center_id')
                    ->where('center_id', $this->center)
                    ->select('a.title as at','a.image', 'a.status', 'a.sort','a.id','c.name', 'ce.center_name as ct');
            }

            $grid->id('ID')->sortable();
            $grid->column('at','title')->display(function ($title){
                return "<div style='width:590px'>$title</div>";
            });
            $grid->column('ct', 'Belong');
            $grid->column('name', 'category');
            $grid->image()->image(Upload_Domain, 100, 100);
            $grid->status()->switch();
            $grid->sort()->editable();
//            $grid->created_at();
//            $grid->updated_at();
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
            $form->textarea('title', 'title')->rules('required|min:3');
            $form->ckeditor('content', 'content');
            $form->image('image', 'image');
            $form->file('file', 'file')->options(['initialPreviewConfig'  => [[ 'type' => 'pdf']]]);
            $form->select('type_id', '类型')->options(['' => '请选择'] + Category::pluck('name', 'id')->toarray())->rules('required');
            if(Admin::user()->inRoles([CENTER_ADMIN, SUPER])){  #超管和中心管理可以替分管写文章
                $form->select('center_id', '中心')
                    ->options(['' => '请选择'] + Center::pluck('center_name', 'id')->toarray())
                    ->rules('required')->value($this->center);
            }else{
                $form->hidden('center_id')->value($this->center);
            }
            $form->hidden('mid')->value($this->mid);
            $form->hidden('sort');
            $form->hidden('status');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
            $form->saved(function (Form $form){
                if($form->model()->file ){
                    $arr = array_only($form->model()->toArray(), ['title','type_id','center_id', 'mid', 'file']);
                    $arr['path'] = $arr['file'];
                    unset($arr['file']);
                    File::create($arr);
                }
            });
        });
    }
}
