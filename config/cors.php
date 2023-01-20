<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['*'],

    'allowed_methods' => ['GET','POST','PUT'],

    'allowed_origins' => ['*','nuedge.opentech4u.co.in','opentech4u.co.in:*'],

    'allowed_origins_patterns' => ['/(.*)\wip/','/(.*)\.test/'],

    'allowed_headers' => ['*','content-type','accept','x-custom-header'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
