<?php

declare(strict_types=1);

use App\Console\MailTestCommand;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\Migrations\Tools\Console\Command\ListCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\UpToDateCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;

return [
    /* Используемые команды в консоли */
    'commands' => [
        ValidateSchemaCommand::class,
        MailTestCommand::class,

        ExecuteCommand::class,
        MigrateCommand::class,
        LatestCommand::class,
        ListCommand::class,
        StatusCommand::class,
        VersionCommand::class,
        UpToDateCommand::class,
        DiffCommand::class,
    ],
];
