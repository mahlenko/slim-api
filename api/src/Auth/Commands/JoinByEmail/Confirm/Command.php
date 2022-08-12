<?php

declare(strict_types=1);

namespace App\Auth\Commands\JoinByEmail\Confirm;

use App\Auth\Repositories\UserRepository;
use App\Auth\Services\Senders\JoinByEmailCompleteSender;
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
     * @var Flasher
     */
    private Flasher $flasher;

    /**
     * @var JoinByEmailCompleteSender
     */
    private JoinByEmailCompleteSender $sender;

    /**
     * @param UserRepository $repository
     * @param Flasher $flasher
     * @param JoinByEmailCompleteSender $sender
     */
    public function __construct(
        UserRepository $repository,
        Flasher $flasher,
        JoinByEmailCompleteSender $sender
    ) {
        $this->repository = $repository;
        $this->flasher = $flasher;
        $this->sender = $sender;
    }

    /**
     * @param Request $request
     */
    public function handle(Request $request): void
    {
        if (!$user = $this->repository->findByConfirmation($request->token)) {
            throw new DomainException('The token is not valid');
        }

        $user->confirm($request->token, new DateTimeImmutable());
        $this->flasher->flush();

        $this->sender->send($user->getEmail());
    }
}
