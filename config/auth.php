<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'guard'     => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */
    'guards' => [

        ## Web Guard :
        'web' => [
            'driver'   => 'session',
            'provider' => 'webs',
        ],

        ## Admin Guard :
        'admin' => [
            'driver'   => 'session',
            'provider' => 'admins',
        ],

        ## Staff Guard :
        'staff' => [
            'driver'   => 'session',
            'provider' => 'staff',
        ],

        ## Franchise Guard :
        'franchise' => [
            'driver'   => 'session',
            'provider' => 'franchise',
        ],

        ## Affiliate Guard :
        'affiliate' => [
            'driver'   => 'session',
            'provider' => 'affiliates',
        ],

        ## API Guard (Sanctum) :
        'api' => [
            'driver'   => 'sanctum',
            'provider' => 'registers',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */
    'providers' => [

        ## Default Users (keep for Laravel internals) :
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Register::class,
        ],

        ## Web Members :
        'webs' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Register::class,
        ],

        ## API Members :
        'registers' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Register::class,
        ],

        ## Admin :
        'admins' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Admin::class,
        ],

        ## Staff :
        'staff' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Staff::class,
        ],

        ## Franchise :
        'franchise' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Franchise::class,
        ],

        ## Affiliate :
        'affiliates' => [
            'driver' => 'eloquent',
            'model'  => App\Models\AffiliateMember::class,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */
    'passwords' => [

        'users' => [
            'provider' => 'users',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],

        'registers' => [
            'provider' => 'registers',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],

        'admins' => [
            'provider' => 'admins',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],

        'staff' => [
            'provider' => 'staff',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],

        'franchise' => [
            'provider' => 'franchise',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],

        'affiliates' => [
            'provider' => 'affiliates',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */
    'password_timeout' => 10800,

];