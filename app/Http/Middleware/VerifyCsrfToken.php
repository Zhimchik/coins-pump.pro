<?php

namespace App\Http\Middleware;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Telegram\Bot\Laravel\Facades\Telegram;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '544943812:*'
    ];

    public function __construct(Application $app, Encrypter $encrypter)
    {
        $this->app = $app;
        $this->encrypter = $encrypter;
        $this->except[] = Telegram::getAccessToken();
    }
}
