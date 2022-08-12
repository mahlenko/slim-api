<?php

declare(strict_types=1);

namespace App\Auth\Services\Senders;

use App\Auth\Entities\Email;

/**
 * Уведомление о смене пароля
 * @package App\Auth\Services\Senders
 */
interface ResetPasswordCompleteSender
{
    /**
     * @param Email $email
     */
    public function send(Email $email): void;
}
