<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HideInfo extends Model
{
    protected $table = 'hide_infos';

    protected $fillable = [
        'user_id',
        'key'
    ];
}
