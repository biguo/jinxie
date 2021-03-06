<?php

namespace App\Providers;

use App\Models\FileType;
use App\Models\Mail;
use App\Models\Project;
use App\Observers\FileTypeObserver;
use App\Observers\MailObserver;
use App\Observers\ProjectObserver;
use App\Observers\UserObserver;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 为 User 模型注册观察者
        Administrator::observe(UserObserver::class);
        Project::observe(ProjectObserver::class);
        FileType::observe(FileTypeObserver::class);
//        Mail::observe(MailObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
