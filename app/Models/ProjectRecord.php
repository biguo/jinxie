<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectRecord extends Model
{
    protected $table = 'project_record';

    protected $fillable = ['json', 'mid', 'center_id', 'description', 'name'];
}
