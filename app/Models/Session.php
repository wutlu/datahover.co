<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $table = 'sessions';

    protected $casts = [
        'id' => 'string'
    ];

    /**
     * Get the user associated with the user.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
