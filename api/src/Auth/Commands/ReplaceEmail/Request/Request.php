<?php

declare(strict_types=1);

namespace App\Auth\Commands\ReplaceEmail\Request;

class Request
{
    /**
     * @var string
     */
    public string $id = '';

    /**
     * @var string
     */
    public string $email = '';
}
