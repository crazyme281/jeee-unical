<?php

namespace App\Services\Paystack;


class Plan extends Base
{
    protected $base = 'plan/';

    public function get($plan_id) {
        $r = $this->client->get($plan_id);
        $res = \GuzzleHttp\json_decode($r->getBody());
        if($res->status != true)
            return false;
        return $res->data;
    }
}