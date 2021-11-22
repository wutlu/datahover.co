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
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'users' => 'array',
    ];

    public function subscriptionEndDate()
    {
        return User::whereIn('id', $this->users)->orderBy('subscription_end_date', 'desc')->value('subscription_end_date');
    }
}
