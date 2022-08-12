<?php

declare(strict_types=1);

use App\Containers\Loaders\LoaderInterface;
use DI\ContainerBuilder;

$config = require __DIR__ . '/../../config/app.php';

$builder = new ContainerBuilder();
$builder->useAnnotations(false);
$builder->enableCompilation($config['temp']);
$builder->writeProxiesToFile(true, $config['temp'] . '/proxies');

$container = $builder->build();

foreach ($config['autoload'] as $name => $object) {
    /* @var LoaderInterface $class */
    $class = $container->get($object);
    $container->set($name, $class->boot());
}

return $container;
