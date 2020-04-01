<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    protected $table = 'mail';


    /**
     * A mail has and belongs to many receiver.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function receivers()
    {
        $relatedModel = config('admin.database.users_model');

        return $this->belongsToMany($relatedModel, 'mail_user', 'mail_id', 'user_id');
    }

    public function sender()
    {
        $relatedModel = config('admin.database.users_model');

        return $this->belongsTo($relatedModel, 'sender_id', 'id');
    }
}
