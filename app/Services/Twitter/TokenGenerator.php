<?php

namespace App\Services\Twitter;

use App\Services\Twitter\OAuth\SignatureMethod_HMAC_SHA1;
use App\Services\Twitter\OAuth\Consumer;
use App\Services\Twitter\OAuth\Request;
use App\Services\Twitter\OAuth\Util;

class TokenGenerator
{
    public $token = null;
    public $timeout = 30;
    public $connecttimeout = 30; 
    public $ssl_verifypeer = false;
    public $useragent = 'TwitterOAuth v0.2.0-beta2';

    function __construct(string $consumer_key, string $consumer_secret)
    {
        $this->sha1_method = new SignatureMethod_HMAC_SHA1();
        $this->consumer = new Consumer($consumer_key, $consumer_secret);
    }

    /**
     * One time exchange of username and password for access token and secret.
     *
     * @var string $username
     * @var string $password
     *
     * @return array("oauth_token" => "the-access-token",
     *                "oauth_token_secret" => "the-access-secret",
     *                "user_id" => "9436992",
     *                "screen_name" => "abraham",
     *                "x_auth_expires" => "0")
     */  
    function getXAuthToken(string $username, string $password)
    {
        $parameters['x_auth_username'] = $username;
        $parameters['x_auth_password'] = $password;
        $parameters['x_auth_mode'] = 'client_auth';

        $request = Request::from_consumer_and_token(
            $this->consumer,
            $this->token,
            'POST',
            'https://api.twitter.com/oauth/access_token',
            $parameters
        );

        $request->sign_request($this->sha1_method, $this->consumer, $this->token);
        $request = $this->http($request->get_normalized_http_url(), $request->to_postdata());

        $token = Util::parse_parameters($request);

        return $token;
    }

    /**
     * Make an HTTP request
     *
     * @return API results
     */
    function http($url, $postfields)
    {
        $ci = curl_init();

        $headers = [ 'Expect:' ];

        /* Curl settings */
        curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
        curl_setopt($ci, CURLOPT_HEADERFUNCTION, [ $this, 'getHeader' ]);
        curl_setopt($ci, CURLOPT_HEADER, false);
        curl_setopt($ci, CURLOPT_POST, true);
        curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLOPT_URL, $url);

        $response = curl_exec($ci);

        curl_close ($ci);

        return $response;
    }

    /**
     * Get the header info to store.
     */
    function getHeader($ch, $header)
    {
        $i = strpos($header, ':');

        if (!empty($i))
        {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));

            $this->http_header[$key] = $value;
        }

        return strlen($header);
    }
}
