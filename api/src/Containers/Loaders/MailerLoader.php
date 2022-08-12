<?php

namespace App\Containers\Loaders;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Mailer\EventListener\EnvelopeListener;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;

class MailerLoader implements LoaderInterface
{

    public function __construct(private \Laminas\Config\Config $config, private Container $container)
    {
    }

    /**
     * @return Mailer|null
     */
    public function boot(): ?Mailer
    {
        $config = $this->config->get('mail');

        $dispatcher = new EventDispatcher();

        $dispatcher->addSubscriber(
            new EnvelopeListener(
                new Address(
                    $config->from->address,
                    $config->from->name,
                )
            )
        );

        try {
            $transport = (new Transport\Smtp\EsmtpTransport(
                $config->host ?? 'mailer',
                $config->port ?? 25,
                $config->encryption === 'tls',
                $dispatcher,
                $this->container->get(LoggerInterface::class)
            ))
                ->setUsername($config->username)
                ->setPassword($config->password);

            return new Mailer($transport);
        } catch (DependencyException | NotFoundException) {
            // ...
        }

        return null;
    }
}
