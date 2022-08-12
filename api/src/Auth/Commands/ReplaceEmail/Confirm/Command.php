<?php

declare(strict_types=1);

namespace App\Auth\Commands\ReplaceEmail\Confirm;

use App\Auth\Repositories\UserRepository;
use App\Auth\Services\Senders\ReplaceEmailCompleteSender;
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
     * @var ReplaceEmailCompleteSender
     */
    private ReplaceEmailCompleteSender $sender;

    /**
     * Command constructor.
     * @param UserRepository $repository
     * @param Flasher $flasher
     * @param ReplaceEmailCompleteSender $sender
     */
    public function __construct(
        UserRepository $repository,
        Flasher $flasher,
        ReplaceEmailCompleteSender $sender
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
        if (!$user = $this->repository->findByConfirmationReplaceEmail($request->token)) {
            throw new DomainException('The token is not valid');
        }

        $user->confirmReplaceEmail($request->token, new DateTimeImmutable());
        $this->flasher->flush();

        $this->sender->send($user->getEmail());
    }
}
