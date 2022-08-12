<?php

declare(strict_types=1);

namespace App\Auth\Commands\JoinByEmail\Confirm;

class Request
{
    /**
     * @var string
     */
    public string $token = '';
}
