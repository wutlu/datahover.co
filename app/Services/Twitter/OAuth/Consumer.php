<?php

namespace App\Services\Twitter\OAuth;

class Consumer
{
    public $key;
    public $secret;

    function __construct(string $key, string $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    function __toString()
    {
        return "OAuthConsumer[key=$this->key,secret=$this->secret]";
    }
}
