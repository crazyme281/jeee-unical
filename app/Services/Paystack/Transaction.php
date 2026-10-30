<?php
namespace App\Services\Paystack;


use Carbon\Carbon;

class Transaction extends Base
{
    protected $base = 'transaction/';

    public function verify($reference)
    {
        $r = $this->client->get("verify/$reference");
        $res = \GuzzleHttp\json_decode($r->getBody());
        if($res->data->status != 'success')
            return false;
        $date = Carbon::parse($res->data->transaction_date);
        if($date->diffInHours() > 24) // old transaction record
            return false;

        return $res->data;
    }
}