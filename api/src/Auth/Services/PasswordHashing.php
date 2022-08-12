<?php

declare(strict_types=1);

namespace App\Auth\Services;

interface PasswordHashing
{
    /**
     * @param string $string
     * @return string
     */
    public function hash(string $string): string;

    /**
     * @param string $string
     * @param string $hash
     * @return bool
     */
    public function validate(string $string, string $hash): bool;
}
