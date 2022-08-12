<?php

declare(strict_types=1);

namespace App\Auth\Commands\ResetPassword\Reset;

class Request
{
    /**
     * @var string
     */
    public string $token = '';

    /**
     * @var string
     */
    public string $password = '';
}
