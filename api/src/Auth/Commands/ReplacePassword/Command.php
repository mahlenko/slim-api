<?php

declare(strict_types=1);

namespace App\Auth\Commands\ReplacePassword;

use App\Auth\Entities\Id;
use App\Auth\Repositories\UserRepository;
use App\Auth\Services\PasswordHashing;
use App\Auth\Services\Senders\ReplacePasswordSender;
use App\Services\Flasher;

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
     * @var ReplacePasswordSender
     */
    private ReplacePasswordSender $sender;

    /**
     * Command constructor.
     * @param UserRepository $repository
     * @param PasswordHashing $hashing
     * @param Flasher $flasher
     */
    public function __construct(
        UserRepository $repository,
        PasswordHashing $hashing,
        Flasher $flasher,
        ReplacePasswordSender $sender
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
        $user = $this->repository->get(new Id($request->id));

        $user->replaceHashPassword(
            $request->old_password,
            $request->password,
            $this->hashing
        );

        $this->flasher->flush();

        $this->sender->send($user->getEmail(), $request->password);
    }
}
