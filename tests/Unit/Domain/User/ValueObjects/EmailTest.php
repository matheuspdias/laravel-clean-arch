<?php

namespace Tests\Unit\Domain\User\ValueObjects;

use App\Domain\User\ValueObjects\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_should_create_valid_email(): void
    {
        $email = new Email('test@example.com');

        $this->assertEquals('test@example.com', $email->value());
    }

    public function test_should_normalize_email_to_lowercase(): void
    {
        $email = new Email('TEST@EXAMPLE.COM');

        $this->assertEquals('test@example.com', $email->value());
    }

    public function test_should_throw_exception_for_invalid_email(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Email inválido');

        new Email('invalid-email');
    }

    public function test_should_throw_exception_for_empty_email(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Email('');
    }

    public function test_should_compare_emails_correctly(): void
    {
        $email1 = new Email('test@example.com');
        $email2 = new Email('test@example.com');
        $email3 = new Email('other@example.com');

        $this->assertTrue($email1->equals($email2));
        $this->assertFalse($email1->equals($email3));
    }

    public function test_should_convert_to_string(): void
    {
        $email = new Email('test@example.com');

        $this->assertEquals('test@example.com', (string) $email);
    }
}