<?php

declare(strict_types=1);

namespace App\Auth\Commands\ReplacePassword;

class Request
{
    /**
     * @var string
     */
    public string $id = '';

    /**
     * @var string
     */
    public string $old_password = '';

    /**
     * @var string
     */
    public string $password = '';
}
