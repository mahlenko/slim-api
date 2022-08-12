<?php

declare(strict_types=1);

use DI\Container;
use Slim\Factory\AppFactory;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

require __DIR__ . '/../vendor/autoload.php';

/* Debugger */
$whoops = new Run();
$whoops->pushHandler(new PrettyPageHandler())->register();

/* @var Container $container */
$container = require __DIR__ . '/../src/Containers/handler.php';
$app = AppFactory::createFromContainer($container);

/* */
(require __DIR__ . '/../src/Middleware/handler.php')($app, $container);

/* Routes application */
require __DIR__ . '/../config/routes.php';

$app->run();
