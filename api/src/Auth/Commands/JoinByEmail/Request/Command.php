<?php

declare(strict_types=1);

namespace App\Auth\Commands\JoinByEmail\Request;

use App\Auth\Entities\Email;
use App\Auth\Entities\Id;
use App\Auth\Entities\User;
use App\Auth\Repositories\UserRepository;
use App\Auth\Services\PasswordHashing;
use App\Auth\Services\Senders\JoinByEmailRequestSender;
use App\Auth\Services\Tokenizer;
use App\Services\Flasher;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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
     * @var PasswordHashing
     */
    private PasswordHashing $hasher;

    /**
     * @var Tokenizer
     */
    private Tokenizer $tokenizer;

    /**
     * @var JoinByEmailRequestSender
     */
    private JoinByEmailRequestSender $pusher;

    /**
     * @param UserRepository $repository
     * @param Flasher $flasher
     * @param PasswordHashing $hasher
     * @param Tokenizer $tokenizer
     * @param JoinByEmailRequestSender $pusher
     */
    public function __construct(
        UserRepository $repository,
        Flasher $flasher,
        PasswordHashing $hasher,
        Tokenizer $tokenizer,
        JoinByEmailRequestSender $pusher
    ) {
        $this->repository = $repository;
        $this->flasher = $flasher;
        $this->hasher = $hasher;
        $this->tokenizer = $tokenizer;
        $this->pusher = $pusher;
    }

    /**
     * @param Request $request
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function handle(Request $request): void
    {
        $email = new Email($request->email);

        if ($this->repository->hasByEmail($email)) {
            throw new DomainException('User already exists');
        }

        $user = User::joinByEmail(
            Id::generate(),
            $email,
            $this->hasher->hash($request->password),
            new DateTimeImmutable(),
            $token = $this->tokenizer->generate(new DateTimeImmutable())
        );

        $this->repository->add($user);
        $this->flasher->flush();

        $this->pusher->send($user->getEmail(), $token);
    }
}
