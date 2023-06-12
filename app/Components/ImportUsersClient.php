<?php


namespace App\Components;


use GuzzleHttp\Client;

class ImportUsersClient
{
    public $client;

    /**
     * ImportUsersClient constructor.
     * @param $client
     */
    public function __construct()
    {
        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => env('USERS_URL', 'https://jsonplaceholder.typicode.com'),
            // You can set any number of default request options.
            'timeout'  => 2.0,
            'verify' => false
        ]);
    }
}
