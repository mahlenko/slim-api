<?php

namespace App\Containers\Loaders;

use DI\Container;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class TwigLoader implements LoaderInterface
{
    public function __construct(private \Laminas\Config\Config $config, private Container $container)
    {
        // ...
    }

    /**
     * @inheritDoc
     */
    public function boot(): \Twig\Environment
    {
        $environment_dev = $this->config->get('app')->environment == 'dev';
        $config = $this->config->get('twig');

        $loader = new FilesystemLoader($config->folder);

        $environment = new Environment($loader, [
            'debug' => $environment_dev,
            'cache' => $environment_dev ? $config->cache : false,
            'auto_reload' => true,
            'strict_variables' => true,
        ]);

        if ($environment_dev) {
            $environment->addExtension(new DebugExtension());
        }

        if ($config->functions) {
            foreach ($config->functions as $class) {
                $function = $this->container->get($class);
                $environment->addFunction($function);
            }
        }

        if ($config->extensions) {
            foreach ($config->extensions as $class) {
                $extension = $this->container->get($class);
                $environment->addExtension($extension);
            }
        }

        return $environment;
    }
}
