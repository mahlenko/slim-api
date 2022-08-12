<?php

declare(strict_types=1);

namespace App\Auth\Commands\JoinByEmail\Request;

class Request
{
    /**
     * @var string
     */
    public string $email = '';

    /**
     * @var string
     */
    public string $password = '';
}
