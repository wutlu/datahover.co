<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Etsetra\Library\DateTime as DT;
use Request;

class Logs extends Model
{
    protected $table = 'logs';

    protected $fillable = [
        'email_sent',
    ];

    /**
     * Get the user associated with the user.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
