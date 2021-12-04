<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    protected $table = 'payment_histories';

    protected $fillable = [
        'user_id',
        'session_id',
        'amount',
        'status',
        'expires_at',
        'meta',
        'series',
        'sequence',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'meta' => 'json'
    ];

    /**
     * Get the user associated with the user.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
