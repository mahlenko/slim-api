<?php

declare(strict_types=1);

namespace App\Auth\Commands\Unblock;

use App\Auth\Entities\Id;
use App\Auth\Repositories\UserRepository;
use App\Auth\Services\Senders\UnblockSender;
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
     * @var UnblockSender
     */
    private UnblockSender $sender;

    /**
     * @param UserRepository $repository
     * @param Flasher $flasher
     * @param UnblockSender $sender
     */
    public function __construct(
        UserRepository $repository,
        Flasher $flasher,
        UnblockSender $sender
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
        $user->unBlock();

        $this->flasher->flush();

        $this->sender->send($user->getEmail());
    }
}
