<?php

namespace App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MailTestCommand extends Command
{
    /**
     * @param MailerInterface $mailer
     * @param Environment $twig
     * @param string|null $name
     */
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('mail:test')
             ->setDescription('Test send mail');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<comment>Sending</comment>');

        try {
            $email = (new Email())
                ->subject('Test mail sender')
                ->to('test-to@app.test')
                ->html($this->twig->render('mail-test.twig'));

            $this->mailer->send($email);
        } catch (TransportExceptionInterface | LoaderError | RuntimeError | SyntaxError $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            return self::FAILURE;
        }

        $output->writeln('<info>Done!</info>');

        return self::SUCCESS;
    }
}
