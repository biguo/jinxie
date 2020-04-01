<?php

namespace App\Observers;

use App\Models\Mail;
use App\Models\MailUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;


class MailObserver
{

    /**
     * 监听数据即将创建的事件。
     *
     * @return void
     */
    public function creating(Mail $mail)
    {

    }

    /**
     * 监听数据创建后的事件。
     *
     * @return void
     */
    public function created(Mail $mail)
    {
//        $redis = Redis::connection('default');
        DB::table('temps')->insert(['cols'=>print_r($mail, true)]);
//        foreach ($mail->receivers() as $item){
////            $mailRead = "mail_read:" . $mail->id . ':' . $item->id;
////            $redis->set($mailRead, 1);
//            DB::table('temps')->insert(['cols'=>print_r($item, true)]);
//        }

    }

    /**
     * 监听数据即将更新的事件。
     *
     * @return void
     */
    public function updating(Mail $mail)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @return void
     */
    public function updated(Mail $mail)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @return void
     */
    public function saving(Mail $mail)
    {

    }

    /**
     * 监听数据保存后的事件。
     *
     * @return void
     */
    public function saved(Mail $mail)
    {

    }

    /**
     * 监听数据即将删除的事件。
     *
     * @return void
     */
    public function deleting(Mail $mail)
    {

    }

    /**
     * 监听数据删除后的事件。
     *
     * @return void
     */
    public function deleted(Mail $mail)
    {
        $attr = ['mail_id' => $mail->id];
        MailUser::where($attr)->delete();
    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @return void
     */
    public function restoring(Mail $mail)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @return void
     */
    public function restored(Mail $mail)
    {

    }
}