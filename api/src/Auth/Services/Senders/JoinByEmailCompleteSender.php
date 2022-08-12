<?php

declare(strict_types=1);

namespace App\Auth\Services\Senders;

use App\Auth\Entities\Email;

/**
 * Отправка письма об успешной регистрации.
 * Письмо отправляется после подтверждения регистрации токеном.
 *
 * Interface JoinByEmailConfirmSender
 * @package App\Auth\Services\Senders
 */
interface JoinByEmailCompleteSender
{
    /**
     * @param Email $email
     */
    public function send(Email $email): void;
}
