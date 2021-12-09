<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proxy extends Model
{
    protected $table = 'proxies';
    protected $fillable = [
        'ip',
        'port',
        'username',
        'password',
        'type',
        'speed',
        'expiry_date',
    ];
}
