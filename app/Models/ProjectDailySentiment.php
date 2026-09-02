<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDailySentiment extends Model
{
    protected $table = 'project_daily_sentiments';

    protected $fillable = [
        'project_id',
        'date',
        'positive',
        'neutral',
        'negative',
        'total',
    ];

    protected $casts = [
        'project_id' => 'integer',
        'date'       => 'date:Y-m-d',
        'positive'   => 'integer',
        'neutral'    => 'integer',
        'negative'   => 'integer',
        'total'      => 'integer',
    ];
}
