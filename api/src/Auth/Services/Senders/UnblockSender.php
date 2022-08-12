<?php

declare(strict_types=1);

namespace App\Auth\Services\Senders;

use App\Auth\Entities\Email;

/**
 * Уведомление о разблокировки аккаунта пользователя
 * @package App\Auth\Services\Senders
 */
interface UnblockSender
{
    /**
     * @param Email $email
     */
    public function send(Email $email): void;
}
