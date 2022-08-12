<?php

declare(strict_types=1);

use DI\Container;
use Psr\Log\LoggerInterface;
use Slim\App;

return static function (App $app, Container $container): void {
    $debug = getenv('APP_DEBUG') === '1';

    $logger = $container->get(LoggerInterface::class);

    $app->addErrorMiddleware($debug, true, true, $logger);
};
