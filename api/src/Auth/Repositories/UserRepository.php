<?php

declare(strict_types=1);

namespace App\Auth\Repositories;

use App\Auth\Entities\Email;
use App\Auth\Entities\Id;
use App\Auth\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use DomainException;
use Webmozart\Assert\Assert;

class UserRepository
{
    /**
     * @var EntityRepository
     */
    protected EntityRepository $repository;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {
        /**
         * @var EntityRepository $repository
         */
        $repository = $this->em->getRepository(User::class);
        $this->repository = $repository;
    }

    /**
     * @param Id $id
     * @return User
     */
    public function get(Id $id): User
    {
        /* @var User $user */
        if (!$user = $this->repository->find($id->getValue())) {
            throw new DomainException('The user doesn\'t exist yet');
        }

        return $user;
    }

    /**
     * @param User $user
     */
    public function add(User $user): void
    {
        $this->em->persist($user);
    }

    /**
     * @param User $user
     */
    public function remove(User $user): void
    {
        $this->em->remove($user);
    }

    /**
     * @param Email $email
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function hasByEmail(Email $email): bool
    {
        return $this->repository->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.email = :email')
                ->setParameter(':email', $email->getValue())
                ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param Email $email
     * @return User
     */
    public function getByEmail(Email $email): User
    {
        if (!$user = $this->findByEmail($email)) {
            throw new DomainException('The user doesn\'t exist yet');
        }

        return $user;
    }

    /**
     * @param Email $email
     * @return User|null
     */
    public function findByEmail(Email $email): ?User
    {
        return $this->repository->findOneBy([
            'email' => $email->getValue()
        ]);
    }

    /**
     * @param string $token
     * @return User|null
     */
    public function findByConfirmation(string $token): ?User
    {
        Assert::notEmpty($token);

        return $this->repository->findOneBy([
            'confirmation.value' => $token
        ]);
    }

    /**
     * @param string $token
     * @return User|null
     */
    public function findByConfirmationReplaceEmail(string $token): ?User
    {
        Assert::notEmpty($token);

        return $this->repository->findOneBy([
            'confirmationReplaceEmail.value' => $token
        ]);
    }

    /**
     * @param string $token
     * @return User|null
     */
    public function findByConfirmationResetHash(string $token): ?User
    {
        Assert::notEmpty($token);

        return $this->repository->findOneBy([
            'confirmationResetHash.value' => $token
        ]);
    }
}
