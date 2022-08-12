<?php

declare(strict_types=1);

namespace App\Auth\Commands\ResetPassword\Request;

class Request
{
    /**
     * @var string
     */
    public string $email = '';
}
