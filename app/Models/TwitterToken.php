<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwitterToken extends Model
{
    protected $table = 'twitter_tokens';
    protected $fillable = [
        'status',
        'screen_name',
        'password',
        'device',
        'consumer_key',
        'consumer_secret',
        'access_token',
        'access_token_secret',
        'tmp_key',
        'value',
        'error_hit',
        'error_reason',
        'created_at',
        'updated_at',
        'pid',
    ];
}
