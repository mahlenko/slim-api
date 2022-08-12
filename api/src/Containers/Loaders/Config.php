<?php

declare(strict_types=1);

namespace App\Containers\Loaders;

use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;

class Config implements LoaderInterface
{
    /**
     * @var bool
     */
    private bool $cache_on = false;

    /**
     * @inheritDoc
     */
    public function boot(): \Laminas\Config\Config
    {
        $cache_folder = __DIR__ . '/../../../var/cache';

        $configs_pattern = '/*[!routes]?.php';
        $configs_default = realpath(__DIR__ . '/../../../config');
        $configs_env = $configs_default . '/' . (getenv('APP_ENV') ?: 'prod');

        $configs = [];
        $alternative = [];
        foreach ([$configs_default, $configs_env] as $folder) {
            foreach (glob($folder . $configs_pattern, GLOB_NOSORT) as $file) {
                $prefix = $this->getPrefixIsFilename($file);

                /**
                 * @var array $array_config
                 * @psalm-suppress
                 */
                if (file_exists($file)) {
                    $array_config = include $file;
                }

                if (key_exists($prefix, $configs)) {
                    $alternative[$prefix] = $array_config;
                } else {
                    $configs[$prefix] = $array_config;
                }
            }
        }

        return new \Laminas\Config\Config(
            (new ConfigAggregator(
                [
                    new ArrayProvider([ ConfigAggregator::ENABLE_CACHE => $this->cache_on ]),
                    new ArrayProvider($configs),
                    new ArrayProvider($alternative)
                ],
                $cache_folder . '/config.php'
            ))->getMergedConfig()
        );
    }

    /**
     * @param string $file
     * @return string
     */
    private function getPrefixIsFilename(string $file): string
    {
        return pathinfo($file, PATHINFO_FILENAME);
    }
}
