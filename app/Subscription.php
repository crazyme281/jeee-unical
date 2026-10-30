<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $guarded = ['id'];

    protected $dates = [
        'trial_ends_at', 'ends_at',
        'created_at', 'updated_at'
    ];

    /**
     * Determine if the subscription is active.
     *
     * @return bool
     */
    public function active()
    {
        if (! is_null($endsAt = $this->ends_at)) {
            return Carbon::now()->lt(Carbon::instance($endsAt));
        } else {
            return false;
        }
    }

    /**
     * Determine if the subscription is no longer active.
     *
     * @return bool
     */
    public function cancelled()
    {
        return ! is_null($this->ends_at);
    }

    /**
     * Determine if the subscription is active, on trial, or within its grace period.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->active() || $this->onGracePeriod();
    }

    /**
     * Determine if the subscription is within its grace period after cancellation.
     *
     * @return bool
     */
    public function onGracePeriod()
    {
        if (! is_null($endsAt = $this->trial_ends_at)) {
            return Carbon::now()->lt(Carbon::instance($endsAt));
        } else {
            return false;
        }
    }
}
