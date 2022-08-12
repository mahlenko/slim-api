<?php

declare(strict_types=1);

namespace App\Auth\Commands\ReplaceEmail\Request;

use App\Auth\Entities\Email;
use App\Auth\Entities\Id;
use App\Auth\Repositories\UserRepository;
use App\Auth\Services\Senders\ReplaceEmailSender;
use App\Auth\Services\Tokenizer;
use App\Services\Flasher;
use DateInterval;
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
     * @var ReplaceEmailSender
     */
    private ReplaceEmailSender $sender;

    /**
     * Command constructor.
     * @param UserRepository $repository
     * @param Flasher $flasher
     * @param ReplaceEmailSender $sender
     */
    public function __construct(
        UserRepository $repository,
        Flasher $flasher,
        ReplaceEmailSender $sender
    ) {
        $this->repository = $repository;
        $this->flasher = $flasher;
        $this->sender = $sender;
    }

    /**
     * @param Request $request
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function handle(Request $request): void
    {
        $user = $this->repository->get(new Id($request->id));

        if (!$this->repository->hasByEmail($email = new Email($request->email))) {
            throw new DomainException('This email is already in use');
        }

        $user->requestReplaceEmail(
            $email,
            $token = (new Tokenizer(new DateInterval('PT1H')))
                ->generate($date = new DateTimeImmutable()),
            $date
        );

        $this->flasher->flush();

        $this->sender->send($email, $token);
    }
}
