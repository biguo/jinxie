<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;

class CustomerAdmin extends Administrator
{

    public function visible($roles)
    {
        if (empty($roles)) {
            return true;
        }

        $roles = array_column($roles, 'slug');

        if ($this->inRoles($roles)) {
            return true;
        }

        return false;
    }
}
