<?php

declare(strict_types=1);

use App\Console\FixtureCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand;

return [
    'commands' => [
        DropCommand::class,
        FixtureCommand::class,
    ]
];
