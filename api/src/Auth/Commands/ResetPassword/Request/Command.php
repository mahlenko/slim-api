<?php

declare(strict_types=1);

namespace App\Auth\Commands\ResetPassword\Request;

use App\Auth\Entities\Email;
use App\Auth\Entities\User;
use App\Auth\Repositories\UserRepository;
use App\Auth\Services\Senders\RequestResetPasswordSender;
use App\Auth\Services\Tokenizer;
use App\Services\Flasher;
use DateInterval;
use DateTimeImmutable;

class Command
{
    /**
     * @var UserRepository
     */
    private UserRepository $repository;

    /**
     * @var Tokenizer
     */
    private Tokenizer $tokenizer;

    /**
     * @var Flasher
     */
    private Flasher $flasher;

    /**
     * @var RequestResetPasswordSender
     */
    private RequestResetPasswordSender $sender;

    /**
     * Command constructor.
     * @param UserRepository $repository
     * @param Tokenizer $tokenizer
     * @param Flasher $flasher
     * @param RequestResetPasswordSender $sender
     */
    public function __construct(
        UserRepository $repository,
        Tokenizer $tokenizer,
        Flasher $flasher,
        RequestResetPasswordSender $sender
    ) {
        $this->repository = $repository;
        $this->tokenizer = $tokenizer;
        $this->flasher = $flasher;
        $this->sender = $sender;
    }

    /**
     * @param Request $request
     */
    public function handle(Request $request): void
    {
        $user = $this->repository->getByEmail(
            $email = new Email($request->email)
        );

        $date = new DateTimeImmutable();
        $tokenizer = new Tokenizer(new DateInterval('PT1H'));

        $user->requestResetHash($token = $tokenizer->generate($date), $date);
        $this->flasher->flush();

        $this->sender->send($user->getEmail(), $token);
    }
}
