<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';

    public function owner() {
        return $this->belongsTo(Administrator::class, 'mid');
    }
}
