<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class CustomerProgressBar extends AbstractDisplayer
{
    public function display($style = 'primary', $size = 'sm', $max = 100)
    {
        $style = collect((array) $style)->map(function ($style) {
            return 'progress-bar-'.$style;
        })->implode(' ');

        return <<<EOT

<div class="progress progress-$size" style="width: 60%;float: left;background-color:#D6D1D1;">
    <div class="progress-bar $style" role="progressbar" aria-valuenow="{$this->value}" aria-valuemin="0" aria-valuemax="$max" style="width: {$this->value}%">
      
    </div>
</div>
<span style="margin-left: 20px;float: left;">{$this->value}%</span>

EOT;
    }
}
