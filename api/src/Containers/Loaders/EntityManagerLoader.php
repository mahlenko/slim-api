<?php

declare(strict_types=1);

namespace App\Containers\Loaders;

use App\Doctrine\Cache;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;

class EntityManagerLoader implements LoaderInterface
{
    public function __construct(
        private Container $container,
        private \Laminas\Config\Config $config
    ) {
    }

    /**
     * @return EntityManager
     * @throws Exception
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ORMException
     */
    public function boot(): EntityManager
    {
        /* @var \Laminas\Config\Config $config */
        $doctrine = $this->config->get('doctrine');

        $cache = new Cache();

        $configuration = Setup::createAnnotationMetadataConfiguration(
            $doctrine->get('entities')->toArray(),
            $doctrine->get('dev_mode'),
            $doctrine->get('proxy_dir'),
            $cache,
            false
        );

        $configuration->setNamingStrategy(new UnderscoreNamingStrategy());

        /*  */
        foreach ($doctrine->get('types')->toArray() as $name => $type) {
            Type::addType($name, $type);
        }

        $eventManager = new EventManager();

        /* Fixed migrate down add created scheme */
        foreach ($doctrine->get('subscribers') as $subscribe) {
            $class = $this->container->get($subscribe);
            $eventManager->addEventSubscriber($class);
        }

        return EntityManager::create($doctrine->get('connection')->toArray(), $configuration, $eventManager);
    }
}
