<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'users',
        ],

        'penyewa' => [
            'driver' => 'session',
            'provider' => 'penyewa',
        ],

        'api-penyewa' => [
            'driver' => 'token',
            'provider' => 'penyewa',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'admin',
        ],

        'api-admin' => [
            'driver' => 'token',
            'provider' => 'admin',
        ],

        'owner' => [
            'driver' => 'session',
            'provider' => 'owner',
        ],

        'api-owner' => [
            'driver' => 'token',
            'provider' => 'owner',
        ],

        'super-owner' => [
            'driver' => 'session',
            'provider' => 'super-owner',
        ],

        'api-super-owner' => [
            'driver' => 'token',
            'provider' => 'super-owner',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\User::class,
        ],

        'penyewa' => [
            'driver' => 'eloquent',
            'model' => App\Models\Penyewa::class,
        ],

        'admin' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],

        'owner' => [
            'driver' => 'eloquent',
            'model' => App\Models\Owner::class,
        ],

        'super-owner' => [
            'driver' => 'eloquent',
            'model' => App\Models\SuperOwner::class,
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
        ],

        'penyewa' => [
            'provider' => 'penyewa',
            'table' => 'password_resets',
            'expire' => 15,
        ],

        'admin' => [
            'provider' => 'admin',
            'table' => 'password_resets',
            'expire' => 15,
        ],

        'owner' => [
            'provider' => 'owner',
            'table' => 'password_resets',
            'expire' => 15,
        ],

        'super-owner' => [
            'provider' => 'super-owner',
            'table' => 'password_resets',
            'expire' => 15,
        ],
    ],

];
