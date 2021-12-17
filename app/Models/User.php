<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;

use App\Models\Payments;

use Etsetra\Library\DateTime as DT;

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
        'is_root' => 'boolean'
    ];

    /**
     * Balance Field
     * 
     * @return float
     */
    public function balance()
    {
        return Payments::where('user_id', $this->id)
            ->where('status', true)
            ->sum('amount');
    }

    /**
     * Subscription
     * 
     * @return object
     */
    public function subscription()
    {
        $hours = (new DT)->diffIn('Hours', $this->subscription_end_date, false);
        $days = intval($hours / 24);

        return (object) [
            'days' => $hours <= 0 ? 0 : ($days == 0 ? 1 : $days),
            'end_date' => $this->subscription_end_date,
            'plan' => Plan::where(
                    $this->plan_id ? [ 'id' => $this->plan_id ] : [ 'name' => 'Trial' ]
                )
                ->first(),
        ];
    }
}
