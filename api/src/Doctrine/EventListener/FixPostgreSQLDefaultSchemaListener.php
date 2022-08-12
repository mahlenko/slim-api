<?php

declare(strict_types=1);

namespace App\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\PostgreSQLSchemaManager;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

class FixPostgreSQLDefaultSchemaListener implements EventSubscriber
{
    /**
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [
            ToolEvents::postGenerateSchema => 'postGenerateSchema'
        ];
    }

    /**
     * @param GenerateSchemaEventArgs $args
     * @psalm-suppress InternalMethod
     * @throws SchemaException|Exception
     */
    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        /* @var PostgreSQLSchemaManager $schemaManager */
        $schemaManager = $args
            ->getEntityManager()
            ->getConnection()
            ->createSchemaManager();

        /**
         * @psalm-suppress UndefinedMethod
         */
        foreach ($schemaManager->getExistingSchemaSearchPaths() as $namespace) {
            if (!$args->getSchema()->hasNamespace($namespace)) {
                $args->getSchema()->createNamespace($namespace);
            }
        }
    }
}
