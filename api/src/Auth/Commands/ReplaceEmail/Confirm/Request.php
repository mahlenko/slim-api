<?php

declare(strict_types=1);

namespace App\Auth\Commands\ReplaceEmail\Confirm;

class Request
{
    /**
     * @var string
     */
    public string $token = '';
}
