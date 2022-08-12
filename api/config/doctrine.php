<?php

declare(strict_types=1);

use App\Auth\Doctrine\Types\EmailType;
use App\Auth\Doctrine\Types\IdType;
use App\Auth\Doctrine\Types\PhoneType;
use App\Auth\Doctrine\Types\RoleType;
use App\Auth\Doctrine\Types\StatusType;

return [
    /* Режим разработки */
    'dev_mode' => false,

    /* Подключение к базе данных */
    'connection' => [
        'driver'   => 'pdo_pgsql',
        'host'     => getenv('DB_HOST'),
        'user'     => '',
        'password' => '',
        'dbname'   => '',
        'charset'  => 'utf-8',
    ],

    /* Дериктория для хранения прокси данных */
    'proxy_dir' => __DIR__ . '/../var/tmp/doctrine/proxies',

    /* Каталог кеширования */
    'cache_dir' => __DIR__ . '/../var/cache/doctrine/' . PHP_SAPI,

    /* Пути к объектам проекта */
    'entities' => [
        realpath(__DIR__ . '/../src/Auth/Entities'),
    ],

    /* Собственные типы для проекта, иначе doctrine не знает как обрабатывать наши объекты */
    'types' => [
        IdType::NAME     => IdType::class,
        EmailType::NAME  => EmailType::class,
        StatusType::NAME => StatusType::class,
        PhoneType::NAME  => PhoneType::class,
        RoleType::NAME   => RoleType::class,
    ],

    /* Список фикстур */
    'fixtures' => [
        realpath(__DIR__ . '/../src/Auth/Doctrine/Fixtures'),
    ],

    /* Подписчики на события doctrine */
    'subscribers' => [
        /* Фикс, убирает в миграции в методе down() строку с созданием схемы */
        App\Doctrine\EventListener\FixPostgreSQLDefaultSchemaListener::class
    ],
];
