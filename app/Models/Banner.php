<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banner';

    public function center() {
        return $this->belongsTo(Center::class);
    }
}
