<?php

declare(strict_types=1);

namespace App\Containers\Loaders;

interface LoaderInterface
{
    public function boot(): mixed;
}
