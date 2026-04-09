<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Copernica API Access Token
    |--------------------------------------------------------------------------
    |
    | Your Copernica REST API v4 access token. You can generate one in the
    | Copernica dashboard under Configuration > API access tokens.
    |
    */

    'access_token' => env('COPERNICA_ACCESS_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Copernica REST API. Use api.copernica.com for
    | standard requests or rest.copernica.com for large dataset retrieval.
    |
    */

    'base_url' => env('COPERNICA_BASE_URL', 'https://api.copernica.com/v4'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The maximum number of seconds to wait for a response from the API.
    |
    */

    'timeout' => env('COPERNICA_TIMEOUT', 30),

];
