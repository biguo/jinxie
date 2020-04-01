<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\CheckRow;
use App\Http\Controllers\Controller;
use App\Models\Mail;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\Redis;

class MailController extends Controller
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

            $content->header('收件箱');
            $content->description('description');

            $content->body($this->grid(0));
        });
    }

    /**
     * Index interface.
     *
     * @return Content
     */
    public function sended()
    {
        return Admin::content(function (Content $content) {

            $content->header('寄件箱');
            $content->description('description');

            $content->body($this->grid(1));
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
     * 回信
     *
     * @param $id
     * @return Content
     */
    public function reply($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('回信');
            $content->description('description');
            $form = Admin::form(Mail::class, function (Form $form) use ($id) {
                $redis = Redis::connection('default');
                $mailRead = "mail_read:" . $id . ':' . $this->mid;
                $weight = $redis->exists($mailRead) ? '500' : '900';

                $mail = Mail::find($id);
                $items = array_map(function ($item) {
                    return '<span style="font-size: 16px;font-weight: 900;">' . $item['name'] . '</span>';
                }, $mail->receivers->toarray());

                $originalContent = "<br><br><hr>寄件人: <span style=\"font-size: 16px;font-weight: 900;\">" . $mail->sender->name . "</span><br>收件人: " . join('&nbsp;&nbsp;&nbsp;', $items) . "" . $mail->content;
                $form->text('subject', '标题')->rules('required|min:3')->value("RE:" . $mail->subject);
                $form->multipleSelect('receivers', '收件人')->options(Administrator::all()->pluck('name', 'id'))->value($mail->sender_id);
                $form->ckeditor('content', 'content')->value($originalContent);
                $form->hidden('sender_id')->value($this->mid);
                $form->setAction(admin_url('mail'));
                $form->tools(function (Form\Tools $tools) {
                    $tools->disableListButton();
                });
            });

            $content->body($form);
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

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid($number)
    {

        return Admin::grid(Mail::class, function (Grid $grid) use ($number) {

            $model = $grid->model()->from('mail as m');
            if ($number === 0) {
                $model->Leftjoin('mail_user as r', 'm.id', '=', 'r.mail_id')
                    ->where('r.user_id', $this->mid)
                    ->select('m.*');
            } else {
                $grid->model()->from('mail as m')
                    ->where('sender_id', $this->mid);
            }
            $grid->id('ID')->sortable();

            if ($number === 0) {
                $redis = Redis::connection('default');
                $mid = $this->mid;
                $grid->subject('主题')->display(function ($item) use ($redis, $mid) {
                    $mailRead = "mail_read:" . $this->id . ':' . $mid;
                    $weight = $redis->exists($mailRead) ? '500' : '900';
                    return "<span style='font-weight:" . $weight . "'>{$item}</span>";
                });
            } else {
                $grid->disableCreation();
                $grid->subject('主题');
            }
            $grid->sender('寄件人')->display(function ($item) {
                return "<span class='label label-success'>{$item['name']}</span>";
            });
            $grid->receivers('收件人')->display(function ($items) {
                $items = array_map(function ($item) {
                    return "<span class='label label-success'>{$item['name']}</span>";
                }, $items);
                return join('&nbsp;', $items);
            });
            $grid->created_at('创建于');
            $grid->updated_at('更新于');

            $grid->disableExport();
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->like('subject', 'Search');
            });

            $grid->actions(function ($actions) {
                // 添加操作
                $actions->append(new CheckRow($actions->getKey()));
                $actions->disableEdit();
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
        return Admin::form(Mail::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->text('subject', '标题')->rules('required');
            $form->multipleSelect('receivers', '收件人')->options(Administrator::all()->pluck('name', 'id'));
            $form->ckeditor('content', 'content');
            $form->hidden('sender_id')->value($this->mid);
        });
    }

}
