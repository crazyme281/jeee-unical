<?php namespace App\Services\Paystack;


use GuzzleHttp\Client;

class Base
{
    protected $client;
    protected $base = null;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.paystack.co/' . $this->base,
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.paystack.secret')
            ]
        ]);
    }
}