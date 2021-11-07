<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function subscription()
    {
        $days = \Carbon\Carbon::now()->diffInDays($this->subscription_end_date, false);

        return (object) [
            'days' => $days >= 0 ? $days : 0,
            'package' => config('subscriptions')[$this->subscription]
        ];
    }

    /**
     * Subscription End Date field full date to date
     * 
     * @param string $value
     * @return string
     */
    public function getSubscriptionEndDateAttribute(string $value)
    {
        return date('Y-m-d', strtotime($value));
    }
}
