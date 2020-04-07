<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\BatchSoftDelete;
use App\Admin\Extensions\CheckRow;
use App\Http\Controllers\Controller;
use App\Models\Mail;
use App\Models\MailUser;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\MessageBag;

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

            $content->header('编辑');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * show interface.
     *
     * @param $id
     * @return Content
     */
    public function show($id)
    {
        MailUser::where(['user_id' => $this->mid, 'mail_id' => $id])->update(['status' => Status_Read]);
        return Admin::content(function (Content $content) use ($id) {

            $content->header('详细页');
            $content->description('description');

            $show = Admin::form(Mail::class, function (Form $form) {

                $form->display('id', 'ID');
                $form->display('subject', '标题');
                $form->multipleSelect('receivers', '收件人')->options(Administrator::all()->pluck('name', 'id'))->readOnly();

                $form->display('content', 'content');
                $form->tools(function (Form\Tools $tools) {
                    $tools->disableListButton();
                });
            });

            $content->body($show->view($id));
        });
    }

    /**
     * 删除邮件
     *
     * @param $params
     * @return mixed
     */
    public function softDelete($params)
    {

        $ids = explode(',', $params);
        $tmp = implode(',', $ids);
        foreach ($ids as $id) {

            $where = ['user_id' => $this->mid, 'mail_id' => $id];
            $relation = MailUser::where($where)->first();
            if ($relation) {
                $relation->status = Status_Deleted;
                $relation->save();
            } else {
                $mail = Mail::find($id);
                $mail->status = Status_Deleted;
                $mail->save();
            }
        }
        if (isAjax()) {
            return response()->json([
                'status' => true,
                'message' => $tmp . trans('admin::lang.delete_succeeded'),
            ]);
        } else {
            return back()->with('toastr', new MessageBag(['message' => $tmp . trans('admin::lang.delete_succeeded')]));
        }
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

            $content->header('写信');
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
                    ->where('r.status','!=', Status_Deleted )
                    ->select('m.*', 'r.status as r_status')
                    ->orderBy('m.created_at','desc');
            } else {
                $grid->model()->from('mail as m')
                    ->where('status','!=', Status_Deleted )
                    ->where('sender_id', $this->mid)
                    ->orderBy('m.created_at','desc');
            }
            $grid->id('ID')->sortable();

            if ($number === 0) {
                $grid->column('主题')->display(function () {
                    $bold = ($this->r_status == Status_Unread)? 900 : 500;
                    return "<span style='font-weight:$bold'>{$this->subject}</span>";
                });
            } else {                # 寄件箱
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
            $grid->disableExport();
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->like('subject', 'Search');
            });

            $grid->actions(function ($actions) {
                // 添加操作
                $actions->append(new CheckRow($actions->getKey()));
                $actions->disableEdit();
                $actions->disableDelete();
            });
            $grid->setView('admin::grid.mailtable');
            $grid->tools(function (Grid\Tools $tools) {
                $tools->batch(function (Grid\Tools\BatchActions $actions) {
                    $actions->disableDelete();
                    $actions->add(trans('admin::lang.delete'), new BatchSoftDelete()); #禁止批量删除之后载入自定义批量删除
                });
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
