<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class CheckRow
{

    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function render()
    {
//        Admin::script($this->script());
        return "<a class=\"btn btn-sm btn-primary\" href='".admin_url('mail').'/'.$this->id."/show'> 查看</a>&nbsp;&nbsp;
<a class=\"btn btn-sm btn btn-danger\" href='".admin_url('mail').'/'.$this->id."/softDelete'> 删除</a>&nbsp;&nbsp;
<a class=' btn btn-sm btn-success' href='".admin_url('mail').'/'.$this->id."/reply'>回信</a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}