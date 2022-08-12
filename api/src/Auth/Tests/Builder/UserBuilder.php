<?php

declare(strict_types=1);

namespace App\Auth\Tests\Builder;

use App\Auth\Entities\Email;
use App\Auth\Entities\Hash;
use App\Auth\Entities\Id;
use App\Auth\Entities\Role;
use App\Auth\Entities\Status;
use App\Auth\Entities\Token;
use App\Auth\Entities\User;
use App\Auth\Services\Tokenizer;
use DateInterval;
use DateTimeImmutable;

class UserBuilder
{
    public Id $id;

    public Email $email;

    public string $hash;

    public Status $status;

    public Token $confirmation;

    public DateTimeImmutable $registration;

    private bool $confirmed = false;

    public function __construct()
    {
        $this->id = Id::generate();
        $this->email = new Email('demo@domain.com');
        $this->hash = 'secret';
        $this->status = Status::wait();
        $this->registration = new DateTimeImmutable();

        $tokenizer = new Tokenizer(new DateInterval('PT1H'));
        $this->confirmation = $tokenizer->generate(new DateTimeImmutable());
    }

    /**
     * @return self
     */
    public function isConfirmed(): self
    {
        $clone = clone $this;
        $clone->confirmed = true;
        return $clone;
    }

    /**
     * @param Token $token
     * @return self
     */
    public function withToken(Token $token): self
    {
        $this->confirmation = $token;
        return $this;
    }

    public function withEmail(Email $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return User
     */
    public function build(): User
    {
        $user = User::joinByEmail(
            $this->id,
            $this->email,
            (new Hash(16))->hash($this->hash),
            $this->registration,
            $this->confirmation
        );

        if ($this->confirmed) {
            $user->confirm(
                $this->confirmation->getValue(),
                $this->confirmation->getExpires()->modify('-1 day')
            );
        }

        return $user;
    }
}
