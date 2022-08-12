<?php

declare(strict_types=1);

namespace Auth\Entities;

use App\Auth\Entities\Phone;
use libphonenumber\NumberParseException;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @package Auth\Entities
 */
class PhoneTest extends TestCase
{
    public function testIncorrect(): void
    {
        $this->expectException(NumberParseException::class);
        Phone::create('incorrect', 'RU');
    }

    public function testValid(): void
    {
        $phone = Phone::create($number = '9851111111', 'RU');
        $this->assertEquals('+7' . $number, $phone->getValue());
    }

    public function testDefault(): void
    {
        $phone = $this->createPhone();
        $phone->setDefault(true);

        $this->assertTrue($phone->isDefault());
    }

    public function testNotByDefault(): void
    {
        $this->assertFalse($this->createPhone()->isDefault());
    }

    public function testHidden(): void
    {
        $phone = $this->createPhone();
        $phone->hidden(true);

        $this->assertTrue($phone->isHidden());
    }

    public function testDisplayed(): void
    {
        $this->assertFalse($this->createPhone()->isHidden());
    }

    public function testNote(): void
    {
        $phone = $this->createPhone();
        $note = 'Office manager';
        $phone->setNote($note);
        $this->assertEquals($note, $phone->getNote());
    }

    public function testEmptyNote(): void
    {
        $phone = $this->createPhone();
        $phone->setNote(' ');
        $this->assertNull($phone->getNote());
    }

    public function testInternational(): void
    {
        $this->assertEquals(
            '+7 985 111-11-11',
            Phone::create('9851111111', 'RU')->international()
        );
    }

    public function testNational(): void
    {
        $this->assertEquals(
            '8 (985) 111-11-11',
            Phone::create('9851111111', 'RU')->national()
        );
    }

    public function testLink(): void
    {
        $this->assertEquals(
            'tel:+7-985-111-11-11',
            Phone::create('9851111111', 'RU')->getLink()
        );
    }

    private function createPhone(): Phone
    {
        return Phone::create('9851111111', 'RU');
    }
}
