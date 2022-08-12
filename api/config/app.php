<?php

declare(strict_types=1);

return [
    /* Окружение */
    'environment' => getenv('APP_ENV') ?: 'prod',

    /* Локаль проекта */
    'locale' => 'RU',

    'temp' => __DIR__ . '/../var/tmp',
    'logs' => __DIR__ . '/../logs',
    'cache' => __DIR__ . '/../var/cache',

    /* Автозагрузка контейнеров */
    'autoload' => [
        \Laminas\Config\Config::class => \App\Containers\Loaders\Config::class,
        \Psr\Log\LoggerInterface::class => \App\Containers\Loaders\LoggerLoader::class,
        \Doctrine\ORM\EntityManagerInterface::class => \App\Containers\Loaders\EntityManagerLoader::class,
        \Doctrine\Migrations\DependencyFactory::class => \App\Containers\Loaders\DependencyFactoryLoader::class,
        \Symfony\Component\Mailer\MailerInterface::class => \App\Containers\Loaders\MailerLoader::class,
        \Twig\Environment::class => \App\Containers\Loaders\TwigLoader::class
    ]
];
