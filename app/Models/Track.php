<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Track extends Model
{
    protected $table = 'tracks';

    protected $fillable = [
        'valid'
    ];

    /**
     * Get the track associated with the user.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
