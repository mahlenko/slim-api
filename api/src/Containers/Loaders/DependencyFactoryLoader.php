<?php

declare(strict_types=1);

namespace App\Containers\Loaders;

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class DependencyFactoryLoader implements LoaderInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private \Laminas\Config\Config $config,
        private LoggerInterface $logger
    ) {
        // ...
    }

    public function boot(): DependencyFactory
    {
        return DependencyFactory::fromEntityManager(
            new ConfigurationArray($this->config->get('migrations')->toArray()),
            new ExistingEntityManager($this->em),
            $this->logger
        );
    }
}
