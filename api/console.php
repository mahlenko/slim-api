<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use DI\Container;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Laminas\Config\Config;
use Symfony\Component\Console\Application;

/* @var Container $container */
$container = require __DIR__ . '/src/Containers/handler.php';

$config = $container->get(Config::class);

$console = $config->get('console');

$application = new Application();

/* Команды с использованием Doctrine */
$em = $container->get(EntityManagerInterface::class);
$application->getHelperSet()->set(new EntityManagerHelper($em), 'em');

/* @var DependencyFactory $dependencyFactory */
$dependencyFactory = $container->get(DependencyFactory::class);

/* Пользовательские комманды */
foreach ($console['commands'] as $command) {
    $application->add($container->make($command, [
        'dependencyFactory' => $dependencyFactory
    ]));
}

try {
    $application->run();
} catch (Exception) {
}
