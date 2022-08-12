<?php

declare(strict_types=1);

namespace App\Auth\Services\Senders;

use App\Auth\Entities\Email;
use App\Auth\Entities\Token;

/**
 * Запрос на сброс пароля
 * @package App\Auth\Services\Senders
 */
interface RequestResetPasswordSender
{
    /**
     * @param Email $email
     * @param Token $token
     */
    public function send(Email $email, Token $token): void;
}
