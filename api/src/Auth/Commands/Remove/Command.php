<?php

declare(strict_types=1);

namespace App\Auth\Commands\Remove;

use App\Auth\Entities\Id;
use App\Auth\Repositories\UserRepository;
use App\Auth\Services\Senders\RemoveAccountSender;
use App\Services\Flasher;

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
     * @var RemoveAccountSender
     */
    private RemoveAccountSender $sender;

    /**
     * Command constructor.
     * @param UserRepository $repository
     * @param Flasher $flasher
     */
    public function __construct(
        UserRepository $repository,
        Flasher $flasher,
        RemoveAccountSender $sender
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
        $user = $this->repository->get(new Id($request->id));
        $email = $user->getEmail();

        $this->repository->remove($user);
        $this->flasher->flush();

        $this->sender->send($email);
    }
}
