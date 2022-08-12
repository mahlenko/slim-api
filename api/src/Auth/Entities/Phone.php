<?php

declare(strict_types=1);

namespace App\Auth\Entities;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberToCarrierMapper;
use libphonenumber\PhoneNumberUtil;

class Phone
{
    /**
     * Код страны
     * @var int
     */
    private int $countryCode;

    /**
     * Номер телефона без кода страны
     * @var string
     */
    private string $number;

    /**
     * Добавочный номер
     * @var string|null
     */
    private ?string $extensionNumber;

    /**
     * Оператор
     * @var string|null
     */
    private ?string $carrier;

    /**
     * Заметка
     * @var string|null
     */
    private ?string $note = null;

    /**
     * Тип номера телефона
     * @var int
     */
    private int $type;

    /**
     * Является номером по-умолчанию
     * @var bool
     */
    private bool $default = false;

    /**
     * Скрытый номер. Виден только администраторам.
     * @var bool
     */
    private bool $hidden = false;

    /**
     * @var PhoneNumberUtil
     */
    private PhoneNumberUtil $util;

    /**
     * @var PhoneNumber
     */
    private PhoneNumber $phoneNumber;

    /**
     * Phone constructor.
     * @param string $raw_number
     * @param string $locale
     * @throws NumberParseException
     */
    public function __construct(string $raw_number, private string $locale)
    {
        $this->util = PhoneNumberUtil::getInstance();

        /* @var PhoneNumber $phoneNumber */
        $phoneNumber = $this->util->parse($raw_number, $locale);
        $this->phoneNumber = $phoneNumber;

        /* Код страны */
        $this->countryCode = (int)$this->phoneNumber->getCountryCode();
        /* Номер телефона */
        $this->number = (string)$this->phoneNumber->getNationalNumber();
        /* Тип номера */
        $this->type = $this->util->getNumberType($this->phoneNumber);
        /* Добавочный номер */
        $this->extensionNumber = $this->phoneNumber->getExtension();
        /* Оператор */
        $this->carrier = $this->getCarrierMapper() ?: null;
    }

    /**
     * @param string $raw_number
     * @param string $locale
     * @return Phone
     * @throws NumberParseException
     */
    public static function create(string $raw_number, string $locale): self
    {
        return new self($raw_number, $locale);
    }

    /**
     * @return int
     */
    public function getCountryCode(): int
    {
        return $this->countryCode;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @return string|null
     */
    public function getExtensionNumber(): ?string
    {
        return $this->extensionNumber;
    }

    /**
     * @return string|null
     */
    public function getCarrier(): ?string
    {
        return $this->carrier;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote(string $note): void
    {
        $this->note = trim($note) ?: null;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->default;
    }

    /**
     * Номер по-умолчанию или нет
     * @param bool $default
     */
    public function setDefault(bool $default): void
    {
        $this->default = $default;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Скрывать номер или нет
     * @param bool $hidden
     */
    public function hidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    /**
     * Местный формат с пробелами 8 (XXX) XXX-XX-XX
     * @return string
     */
    public function national(): string
    {
        return $this->util->format($this->phoneNumber, PhoneNumberFormat::NATIONAL);
    }

    /**
     * Вернет номер телефона в международном формате с пробелами
     * @return string
     */
    public function international(): string
    {
        return $this->util->format($this->phoneNumber, PhoneNumberFormat::INTERNATIONAL);
    }

    /**
     * Формат для ссылки: tel:+XXXXXXXXXXX
     * @return string
     */
    public function getLink(): string
    {
        return $this->util->format($this->phoneNumber, PhoneNumberFormat::RFC3966);
    }

    /**
     * Международный формат без пробелов
     * @return string
     */
    public function getValue(): string
    {
        return $this->util->format($this->phoneNumber, PhoneNumberFormat::E164);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getValue();
    }

    /**
     * Имя оператора
     *
     * Безопасная проверка, вернет пустую строку если страна поддерживает
     * переносимость номера от одного оператора к другому.
     *
     * @param bool $safe
     * @return string
     */
    private function getCarrierMapper(bool $safe = false): string
    {
        $carrierMapper = PhoneNumberToCarrierMapper::getInstance();
        if ($safe) {
            return $carrierMapper->getSafeDisplayName($this->phoneNumber, $this->locale);
        } else {
            return $carrierMapper->getNameForNumber($this->phoneNumber, $this->locale);
        }
    }
}
