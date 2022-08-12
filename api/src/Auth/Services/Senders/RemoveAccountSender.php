<?php

declare(strict_types=1);

namespace App\Auth\Services\Senders;

use App\Auth\Entities\Email;

/**
 * Уведомление пользователю об удалении его из системы
 * @package App\Auth\Services\Senders.
 */
interface RemoveAccountSender
{
    /**
     * @param Email $email
     */
    public function send(Email $email): void;
}
