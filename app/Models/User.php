<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, Billable;

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
        'balance' => 'float',
    ];

    public function subscription()
    {
        $days = \Carbon\Carbon::now()->diffInDays($this->subscription_end_date, false);

        return (object) [
            'days' => $days >= 0 ? $days : 0,
            'plan' => config('plans')[$this->subscription]
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
