<?php

namespace App;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPasswordNotification as ResetPasswordNotification;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable, CrudTrait;

    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'card' => 'array'
    ];

    public function subscriptions() {
        return $this->hasMany(Subscription::class);
    }

    /**
     * @param null $plan
     * @return bool|Subscription
     */
    public function getSubscription($plan = null) {
        $subscriptions = $this->subscriptions;
        if($subscriptions->isEmpty())
            return false;
        $subscription = null;

        if($plan) {
            $subscription = $subscriptions->where('paystack_plan', $plan)->last();
            if($subscription == null)
                return false;
        }
        else
            $subscription = $subscriptions->last();

        return $subscription;
    }

    public function subscribedToCurrentVolume() {
        $current_issue = Issue::current()->first();
        if (!$current_issue) {
            return false;
        }
        $volume = $current_issue->volume;
        return $this->subscriptions()->where('volume', $volume)->where('issue', 0)->exists();
    }

    public function subscribedToIssue($issue_id){
        $issue = Issue::find($issue_id);
        if (!$issue) {
            return false;
        }
        // If subscribed to all the volume issues
        return $this->subscriptions()->where('volume', $issue->volume)->whereIn('issue', [0, $issue->number])->exists();
    }

    public function manuscripts() {
        return $this->hasMany(Manuscript::class);
    }

    public function getIsAdminAttribute() {
        return !!$this->getAttribute('admin');
    }

    public function getNameAttribute() {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function setPasswordAttribute($value) {
        if($value)
            $this->attributes['password'] = Hash::isHashed($value) ? $value : Hash::make($value);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
