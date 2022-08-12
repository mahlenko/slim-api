<?php

declare(strict_types=1);

namespace App\Auth\Entities;

use App\Auth\Services\PasswordHashing;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity()
 * @ORM\Table(name="accounts")
 */
class User
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="account_id")
     */
    private Id $id;

    /**
     * @var Email
     * @ORM\Column(type="account_email", length=50, unique=true)
     */
    private Email $email;

    /**
     * @var Phone|null
     * @ORM\Column(type="account_phone", nullable=true, unique=true)
     */
    private ?Phone $phone = null;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $hash = null;

    /**
     * @var Profile
     * @ORM\OneToOne(targetEntity="Profile", mappedBy="user", cascade={"all"}, orphanRemoval=true)
     */
    private Profile $profile;

    /**
     * @var Status
     * @ORM\Column(type="account_status", length=16)
     */
    private Status $status;

    /**
     * @var Role
     * @ORM\Column(type="account_role", length=16)
     */
    private Role $role;

    /**
     * @var DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $confirmed = null;

    /**
     * @var Token|null
     * @ORM\Embedded(class="App\Auth\Entities\Token")
     */
    private ?Token $confirmation = null;

    /**
     * @var Email|null
     * @ORM\Column(type="account_email", nullable=true)
     */
    private ?Email $replaceEmail = null;

    /**
     * @var Token|null
     * @ORM\Embedded(class="App\Auth\Entities\Token")
     */
    private ?Token $confirmationReplaceEmail = null;

    /**
     * @var Token|null
     * @ORM\Embedded(class="App\Auth\Entities\Token")
     */
    private ?Token $confirmationResetHash = null;

    /**
     * @var DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $blocked = null;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $registered;

    /**
     * User constructor.
     * @param Id $id
     * @param Email $email
     * @param DateTimeImmutable $registered
     * @param Status $status
     * @param Role $role
     * @param Profile $profile
     */
    private function __construct(
        Id $id,
        Email $email,
        DateTimeImmutable $registered,
        Status $status,
        Role $role,
        Profile $profile
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->registered = $registered;
        $this->status = $status;
        $this->role = $role;

        $profile->setUser($this);
        $this->profile = $profile;
    }

    /**
     * @param Id $id
     * @param Email $email
     * @param string $hash
     * @param DateTimeImmutable $registration
     * @param Token $confirmation
     * @param Profile|null $profile
     * @return self
     */
    public static function joinByEmail(
        Id $id,
        Email $email,
        string $hash,
        DateTimeImmutable $registration,
        Token $confirmation,
        Profile $profile = null
    ): self {
        $user = new self($id, $email, $registration, Status::wait(), Role::user(), $profile ?? new Profile());
        $user->confirmation = $confirmation;
        $user->hash = $hash;

        return $user;
    }

    /**
     * Ожидает подтверждения
     * @return bool
     */
    public function isWait(): bool
    {
        return $this->getStatus()->isWait();
    }

    /**
     * Администратор
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->getRole()->isAdmin();
    }

    /**
     * @param string $role
     */
    public function changeRole(string $role): void
    {
        $this->role = Role::set($role);
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->getStatus()->isConfirmed();
    }

    /**
     * Подтверждение с помощью токена
     * @param string $token
     * @param DateTimeImmutable $date
     */
    public function confirm(string $token, DateTimeImmutable $date): void
    {
        if ($this->getConfirmed()) {
            throw new DomainException('Confirmation is not required');
        }

        if (!$confirmation = $this->getConfirmation()) {
            throw new DomainException('Confirmation was not requested');
        }

        $confirmation->validate($token, $date);

        $this->confirmed = $date;
        $this->status = Status::confirmed();
        $this->confirmation = null;
    }

    /**
     * Заменить e-mail адрес пользователя
     * @param Email $email
     * @param Token $token
     * @param DateTimeImmutable $date
     */
    public function requestReplaceEmail(Email $email, Token $token, DateTimeImmutable $date): void
    {
        if (!$this->isConfirmed()) {
            throw new DomainException('User is not active');
        }

        if ($this->getEmail()->isEqualTo($email->getValue())) {
            throw new DomainException('Email is already same');
        }

        if ($this->getConfirmationReplaceEmail() && !$this->getConfirmationReplaceEmail()?->isExpiredTo($date)) {
            throw new DomainException('Replace email is already requested');
        }

        $this->replaceEmail = $email;
        $this->confirmationReplaceEmail = $token;
    }

    /**
     * @param string $token
     * @param DateTimeImmutable $date
     */
    public function confirmReplaceEmail(string $token, DateTimeImmutable $date): void
    {
        if (!$confirmation = $this->getConfirmationReplaceEmail()) {
            throw new DomainException('Confirmation was not requested');
        }

        $confirmation->validate($token, $date);

        $replace_email = $this->getReplaceEmail();
        if ($replace_email) {
            $this->email = $replace_email;
            $this->replaceEmail = null;
            $this->confirmationReplaceEmail = null;
        }
    }

    /**
     * @param string $old_password
     * @param string $new_password
     * @param PasswordHashing $hashing
     */
    public function replaceHashPassword(
        string $old_password,
        string $new_password,
        PasswordHashing $hashing
    ): void {
        if ($hash = $this->getHash()) {
            if (!$hashing->validate($old_password, $hash)) {
                throw new DomainException('Incorrect current password');
            }
        }

        $this->hash = $hashing->hash($new_password);
    }

    /**
     * Запрос на сброс пароля
     * @param Token $token
     * @param DateTimeImmutable $date
     */
    public function requestResetHash(Token $token, DateTimeImmutable $date): void
    {
        if ($this->getConfirmationResetHash() && !$this->getConfirmationResetHash()?->isExpiredTo($date)) {
            throw new DomainException('Reset password is already requested');
        }

        $this->confirmationResetHash = $token;
    }

    /**
     * @param string $token
     * @param string $password
     * @param DateTimeImmutable $date
     * @param PasswordHashing $hashing
     */
    public function resetHash(
        string $token,
        string $password,
        DateTimeImmutable $date,
        PasswordHashing $hashing
    ): void {
        if (!$confirmation = $this->getConfirmationResetHash()) {
            throw new DomainException('Reset password was not requested');
        }

        $confirmation->validate($token, $date);

        $this->confirmationResetHash = null;
        $this->hash = $hashing->hash($password);
    }

    /**
     * Блокировка
     * @param DateTimeImmutable $date
     */
    public function block(DateTimeImmutable $date): void
    {
        $this->blocked = $date;
        $this->status = Status::blocked();
    }

    /**
     * Разблокировка
     */
    public function unBlock(): void
    {
        $this->blocked = null;
        $this->status = $this->getConfirmed()
            ? Status::confirmed()
            : Status::wait();
    }

    /**
     * Заблокированный
     * @return bool
     */
    public function isBlocked(): bool
    {
        return $this->getStatus()->isBlocked() && $this->blocked !== null;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return Phone|null
     */
    public function getPhone(): ?Phone
    {
        return $this->phone;
    }

    /**
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }

    /**
     * @return Profile
     */
    public function getProfile(): Profile
    {
        return $this->profile;
    }

    /**
     * @param Profile $profile
     */
    public function setProfile(Profile $profile): void
    {
        $this->profile = $profile;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getConfirmed(): ?DateTimeImmutable
    {
        return $this->confirmed;
    }

    /**
     * @return Token|null
     */
    public function getConfirmation(): ?Token
    {
        return $this->confirmation;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getBlocked(): ?DateTimeImmutable
    {
        return $this->blocked;
    }

    /**
     * @return Email|null
     */
    public function getReplaceEmail(): ?Email
    {
        return $this->replaceEmail;
    }

    /**
     * @return Token|null
     */
    public function getConfirmationReplaceEmail(): ?Token
    {
        return $this->confirmationReplaceEmail;
    }

    /**
     * @return Token|null
     */
    public function getConfirmationResetHash(): ?Token
    {
        return $this->confirmationResetHash;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getRegistered(): DateTimeImmutable
    {
        return $this->registered;
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkEmbeds(): void
    {
        if ($this->getConfirmation() && $this->getConfirmation()?->isEmpty()) {
            $this->confirmation = null;
        }

        if ($this->getConfirmationReplaceEmail() && $this->getConfirmationReplaceEmail()?->isEmpty()) {
            $this->confirmationReplaceEmail = null;
        }

        if ($this->getConfirmationResetHash() && $this->getConfirmationResetHash()?->isEmpty()) {
            $this->confirmationResetHash = null;
        }
    }
}
