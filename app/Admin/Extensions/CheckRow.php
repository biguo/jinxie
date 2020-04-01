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

    protected function script()
    {

        return <<<SCRIPT

$('.fa-arrow-left').on('click', function () {

    // Your code.
 console.log($(this).data('id'));
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());
        return "<a class='grid-check-row' title='回信' href='".admin_url('mail').'/'.$this->id."/reply'><i class=\"fa fa-arrow-left\"></i></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}