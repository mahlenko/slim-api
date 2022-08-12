<?php

declare(strict_types=1);

namespace App\Auth\Commands\ResetPassword\Reset;

use App\Auth\Repositories\UserRepository;
use App\Auth\Services\PasswordHashing;
use App\Auth\Services\Senders\ResetPasswordCompleteSender;
use App\Services\Flasher;
use DateTimeImmutable;
use DomainException;

class Command
{
    /**
     * @var UserRepository
     */
    private UserRepository $repository;

    /**
     * @var PasswordHashing
     */
    private PasswordHashing $hashing;

    /**
     * @var Flasher
     */
    private Flasher $flasher;

    /**
     * @var ResetPasswordCompleteSender
     */
    private ResetPasswordCompleteSender $sender;

    /**
     * Command constructor.
     * @param UserRepository $repository
     * @param PasswordHashing $hashing
     * @param Flasher $flasher
     * @param ResetPasswordCompleteSender $sender
     */
    public function __construct(
        UserRepository $repository,
        PasswordHashing $hashing,
        Flasher $flasher,
        ResetPasswordCompleteSender $sender
    ) {
        $this->repository = $repository;
        $this->hashing = $hashing;
        $this->flasher = $flasher;
        $this->sender = $sender;
    }

    /**
     * @param Request $request
     */
    public function handle(Request $request): void
    {
        if (!$user = $this->repository->findByConfirmationResetHash($request->token)) {
            throw new DomainException('The token is not valid');
        }

        $user->resetHash($request->token, $request->password, new DateTimeImmutable(), $this->hashing);
        $this->flasher->flush();

        $this->sender->send($user->getEmail());
    }
}
