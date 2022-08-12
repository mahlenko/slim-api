<?php

declare(strict_types=1);

namespace App\Auth\Doctrine\Fixtures;

use App\Auth\Entities\Email;
use App\Auth\Entities\Hash;
use App\Auth\Entities\Id;
use App\Auth\Entities\Profile;
use App\Auth\Entities\User;
use App\Auth\Services\Tokenizer;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends AbstractFixture
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        $user = User::joinByEmail(
            new Id('00000000-0000-0000-0000-000000000001'),
            new Email('demo@domain.com'),
            (new Hash())->hash('secret'),
            new DateTimeImmutable(),
            (new Tokenizer(new DateInterval('PT1H')))->generate(new DateTimeImmutable()),
        );

        $user->getProfile()->setName('Иван');
        $user->getProfile()->setLastName('Васильев');

        $manager->persist($user);
        $manager->flush();
    }
}
