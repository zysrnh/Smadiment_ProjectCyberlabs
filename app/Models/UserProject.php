<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProject extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}