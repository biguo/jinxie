<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('priority')->default('1')->comment('优先级 从1到6')->nullable();
            $table->string('category')->comment('类别  可为空')->nullable();
            $table->integer('from')->comment('由谁指派 id')->nullable();
            $table->integer('to')->comment('指派给谁 id')->nullable();
            $table->tinyInteger('status')->comment('状态')->nullable();
            $table->dateTime('begin_date')->comment('开始时间')->nullable();
            $table->dateTime('end_date')->comment('计划结束时间')->nullable();
            $table->string('title')->comment('主题')->nullable();
            $table->text('description')->comment('描述')->nullable();
            $table->integer('percent')->comment('完成百分比')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project');
    }
}
