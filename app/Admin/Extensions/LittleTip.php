<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Tools\AbstractTool;

class LittleTip extends AbstractTool
{

    protected $description;

    public function __construct($description)
    {
        $this->description = $description;
    }

    /**
     * Render refresh button of grid.
     *
     * @return string
     */
    public function render()
    {
        return <<<EOT
$this->description
EOT;
    }


}
