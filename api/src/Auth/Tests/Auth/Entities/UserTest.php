<?php

declare(strict_types=1);

namespace Auth\Entities;

use App\Auth\Entities\Email;
use App\Auth\Entities\Hash;
use App\Auth\Entities\Id;
use App\Auth\Entities\Role;
use App\Auth\Entities\Token;
use App\Auth\Entities\User;
use App\Auth\Tests\Builder\UserBuilder;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @package Auth\Entities
 */
class UserTest extends TestCase
{
    public function testConfirm(): void
    {
        /* @psalm-var UserBuilder $user */
        $user = (new UserBuilder())
            ->withToken(new Token(
                $token = Uuid::uuid4()->toString(),
                (new DateTimeImmutable())->modify('+1 hours')
            ))
            ->build();

        $user->confirm($token, $date = new DateTimeImmutable());

        $this->assertTrue($user->isConfirmed());
        $this->assertFalse($user->isWait());
        $this->assertEquals($date, $user->getConfirmed());
    }

    public function testConfirmExpires(): void
    {
        $user = (new UserBuilder())
            ->withToken(new Token(
                $token = Uuid::uuid4()->toString(),
                $date = new DateTimeImmutable()
            ))->build();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Token is expired.');
        $user->confirm($token, $date->modify('+1 sec'));
    }

    public function testBlock(): void
    {
        $user = (new UserBuilder())->build();

        $this->assertFalse($user->isBlocked());

        $user->block($date = new DateTimeImmutable());
        $this->assertTrue($user->isBlocked());
    }

    public function testUnBlock(): void
    {
        $user = (new UserBuilder())->build();

        $user->block(new DateTimeImmutable());

        $user->unBlock();

        $this->assertFalse($user->isBlocked());
    }

    public function testSuccess(): void
    {
        $user = User::joinByEmail(
            $id = Id::generate(),
            $email = new Email($email = 'demo@domain.com'),
            $password = 'password',
            $registration = new DateTimeImmutable(),
            $token = new Token(
                Uuid::uuid4()->toString(),
                (new DateTimeImmutable())->modify('+1 hours')
            )
        );

        $this->assertEquals($id, $user->getId());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($password, $user->getHash());
        $this->assertEquals($registration, $user->getRegistered());
        $this->assertNotNull($user->getConfirmation());
    }

    public function testRequestReplaceEmail(): void
    {
        $user = (new UserBuilder())
            ->isConfirmed()
            ->withEmail(new Email('old@domain.com'))
            ->build();

        $user->requestReplaceEmail(
            $new_email = new Email('new-email@domain.com'),
            $token = new Token(Uuid::uuid4()->toString(), $date = new DateTimeImmutable()),
            $date
        );

        $this->assertEquals($new_email, $user->getReplaceEmail());
        $this->assertNotNull($user->getConfirmationReplaceEmail());
        $this->assertEquals($token, $user->getConfirmationReplaceEmail());
    }

    public function testReplaceEmailSame(): void
    {
        $user = (new UserBuilder())
            ->isConfirmed()
            ->withEmail($old = new Email('old@domain.com'))
            ->build();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Email is already same');

        $user->requestReplaceEmail(
            $old,
            $token = new Token(Uuid::uuid4()->toString(), $date = new DateTimeImmutable()),
            $date
        );
    }

    public function testConfirmReplaceEmail(): void
    {
        $user = (new UserBuilder())
            ->isConfirmed()
            ->build();

        $user->requestReplaceEmail(
            $new_email = new Email('new-mail@domain.com'),
            $token = new Token(Uuid::uuid4()->toString(), $date = new DateTimeImmutable()),
            $date
        );

        $user->confirmReplaceEmail($token->getValue(), $date->modify('-1 secs'));

        $this->assertNull($user->getReplaceEmail());
        $this->assertNull($user->getConfirmationReplaceEmail());
        $this->assertEquals($new_email, $user->getEmail());
    }

    public function testNotExpiredRequestReplaceEmail(): void
    {
        $user = (new UserBuilder())
            ->isConfirmed()
            ->withEmail(new Email('old@domain.com'))
            ->build();

        $date = new DateTimeImmutable();

        $user->requestReplaceEmail(
            new Email('new-email@domain.com'),
            new Token(Uuid::uuid4()->toString(), $date->modify('+1 days')),
            $date
        );

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Replace email is already requested');

        $user->requestReplaceEmail(
            new Email('new-email@domain.com'),
            new Token(Uuid::uuid4()->toString(), $date->modify('+1 min')),
            $date
        );
    }

    public function testChangeRole(): void
    {
        $user = (new UserBuilder())
            ->build();

        $user->changeRole(Role::ADMIN);
        $this->assertTrue($user->isAdmin());
    }

    public function testRequestResetPassword(): void
    {
        $user = (new UserBuilder())
            ->build();

        $token = new Token(
            Uuid::uuid4()->toString(),
            (new DateTimeImmutable())->modify('+1 day')
        );

        $user->requestResetHash($token, $date = new DateTimeImmutable());
        $this->assertEquals($token, $user->getConfirmationResetHash());
    }

    public function testRequestResetExist(): void
    {
        $user = (new UserBuilder())
            ->build();

        $token = new Token(
            Uuid::uuid4()->toString(),
            (new DateTimeImmutable())->modify('+1 day')
        );

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Reset password was not requested');
        $user->resetHash($token->getValue(), 'new-password', $date = new DateTimeImmutable(), new Hash());
    }

    public function testResetSuccess(): void
    {
        $user = (new UserBuilder())
            ->build();

        $old_password_hash = $user->getHash();

        $token = new Token(
            Uuid::uuid4()->toString(),
            (new DateTimeImmutable())->modify('+1 day')
        );

        $user->requestResetHash($token, $date = new DateTimeImmutable());
        $user->resetHash(
            $token->getValue(),
            $new_password = 'new-password',
            $date,
            $hasher = new Hash()
        );

        $this->assertTrue($hasher->validate($new_password, (string) $user->getHash()));
        $this->assertNull($user->getConfirmationResetHash());
        $this->assertNotEquals($old_password_hash, $user->getHash());
    }

    public function testReplaceHashIncorrect(): void
    {
        $user = (new UserBuilder())
            ->build();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Incorrect current password');
        $user->replaceHashPassword('', 'secret', $hasher = new Hash());
    }

    public function testReplaceHashSuccess(): void
    {
        $user = (new UserBuilder())
            ->build();

        $old_hash = $user->getHash();
        $user->replaceHashPassword('secret', $hash = 'new-secret', $hasher = new Hash());

        $this->assertNotEquals($old_hash, $user->getHash());
    }

    public function testCreateProfile(): void
    {
        $user = (new UserBuilder())
            ->build();

        $this->assertNotNull($user->getProfile());
    }
}
