<?php

declare(strict_types=1);

namespace App\Containers\Loaders;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Загрузчик логирования работы системы.
 * Использует Monolog в качестве логера.
 */
class LoggerLoader implements LoaderInterface
{
    public function __construct(private \Laminas\Config\Config $config)
    {
    }

    /**
     * Loader Logger
     *
     * @return Logger
     */
    public function boot(): Logger
    {
        $logger = new Logger('API');
        $handler = new StreamHandler(
            $this->config->get('app')->logs . DIRECTORY_SEPARATOR . PHP_SAPI . '.log',
            Logger::WARNING
        );

        $logger->pushHandler($handler);

        return $logger;
    }
}
