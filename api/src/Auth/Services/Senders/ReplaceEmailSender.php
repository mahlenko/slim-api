<?php

declare(strict_types=1);

namespace App\Auth\Services\Senders;

use App\Auth\Entities\Email;
use App\Auth\Entities\Token;

/**
 * Отправка письма с токеном подтверждения на новый email пользователя
 * @package App\Auth\Services\Senders
 */
interface ReplaceEmailSender
{
    /**
     * @param Email $email
     * @param Token $token
     */
    public function send(Email $email, Token $token): void;
}
