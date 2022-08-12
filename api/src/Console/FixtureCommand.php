<?php

declare(strict_types=1);

namespace App\Console;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Config\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @property EntityManagerInterface $em
 * @property Config $config
 */
class FixtureCommand extends Command
{
    /**
     * @param Config $config
     * @param EntityManagerInterface $em
     * @param string|null $name
     */
    public function __construct(
        private Config $config,
        private EntityManagerInterface $em,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('doctrine:fixture')
            ->setAliases(['fixture'])
            ->setDescription('Load fixtures');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Running fixtures...</info>');

        $loader = new Loader();

        /* @psalm-suppress PossiblyNullOperand */
        foreach ($this->config->doctrine->fixtures as $fixture) {
            $loader->loadFromDirectory($fixture);
        }

        $executor = new ORMExecutor($this->em, new ORMPurger());

        $executor->setLogger(static function (string $message) use ($output) {
            $output->writeln(sprintf('<comment>%s</comment>', $message));
        });

        $executor->execute($loader->getFixtures());

        $output->writeln('<info>DONE!</info>');

        return self::SUCCESS;
    }
}
