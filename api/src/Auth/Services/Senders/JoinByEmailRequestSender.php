<?php

declare(strict_types=1);

namespace App\Auth\Services\Senders;

use App\Auth\Entities\Email;
use App\Auth\Entities\Token;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * Отправка письма с токеном подтверждения регистрации
 *
 * Interface JoinByEmailRequestSender
 * @package App\Auth\Services\Senders
 */
class JoinByEmailRequestSender
{
    /**
     * @param MailerInterface $mailer
     * @param array $from
     */
    public function __construct(private MailerInterface $mailer, private array $from)
    {
        // ...
    }

    /**
     * @param Email $email
     * @param Token $token
     * @throws TransportExceptionInterface
     */
    public function send(Email $email, Token $token): void
    {
        $message = (new \Symfony\Component\Mime\Email())
            ->to($email->getValue())
            ->from(new Address($this->from[0], $this->from[1] ?? ''))
            ->subject('Join confirmation')
            ->text('/join/confirm?' . http_build_query([
                'token' => $token->getValue()
                ]));

        $this->mailer->send($message);
    }
}
