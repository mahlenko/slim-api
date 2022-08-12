<?php

declare(strict_types=1);

namespace App\Auth\Commands\Block;

use App\Auth\Entities\Id;
use App\Auth\Repositories\UserRepository;
use App\Auth\Services\Senders\BlockedSender;
use App\Services\Flasher;
use DateTimeImmutable;

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
     * @var BlockedSender
     */
    private BlockedSender $sender;

    /**
     * @param UserRepository $repository
     * @param Flasher $flasher
     * @param BlockedSender $sender
     */
    public function __construct(
        UserRepository $repository,
        Flasher $flasher,
        BlockedSender $sender
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
        $user->block(new DateTimeImmutable());

        $this->flasher->flush();

        $this->sender->send($user->getEmail());
    }
}
