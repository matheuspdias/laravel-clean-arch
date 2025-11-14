<?php

namespace Tests\Unit\Domain\User\Entities;

use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_should_create_new_user(): void
    {
        $user = User::create(
            'John Doe',
            'john@example.com',
            'password123'
        );

        $this->assertEquals('John Doe', $user->name());
        $this->assertEquals('john@example.com', $user->email()->value());
        $this->assertNotEmpty($user->id()->value());
        $this->assertTrue(password_verify('password123', $user->password()));
    }

    public function test_should_throw_exception_for_empty_name(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Nome não pode ser vazio');

        User::create('', 'john@example.com', 'password123');
    }

    public function test_should_throw_exception_for_short_name(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Nome deve ter no mínimo 3 caracteres');

        User::create('Jo', 'john@example.com', 'password123');
    }

    public function test_should_update_user_profile(): void
    {
        $user = User::create(
            'John Doe',
            'john@example.com',
            'password123'
        );

        $originalUpdatedAt = $user->updatedAt();
        sleep(1); // Garante que o timestamp será diferente

        $user->updateProfile('Jane Doe', 'jane@example.com');

        $this->assertEquals('Jane Doe', $user->name());
        $this->assertEquals('jane@example.com', $user->email()->value());
        $this->assertGreaterThan($originalUpdatedAt, $user->updatedAt());
    }

    public function test_should_change_password(): void
    {
        $user = User::create(
            'John Doe',
            'john@example.com',
            'password123'
        );

        $user->changePassword('newpassword456');

        $this->assertTrue(password_verify('newpassword456', $user->password()));
        $this->assertFalse(password_verify('password123', $user->password()));
    }

    public function test_should_have_created_at_and_updated_at(): void
    {
        $user = User::create(
            'John Doe',
            'john@example.com',
            'password123'
        );

        $this->assertInstanceOf(\DateTimeImmutable::class, $user->createdAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->updatedAt());
    }

    public function test_should_construct_user_with_all_parameters(): void
    {
        $id = UserId::generate();
        $email = new Email('test@example.com');
        $createdAt = new \DateTimeImmutable('2023-01-01 10:00:00');
        $updatedAt = new \DateTimeImmutable('2023-01-02 11:00:00');

        $user = new User(
            $id,
            'Test User',
            $email,
            'hashed_password',
            $createdAt,
            $updatedAt
        );

        $this->assertEquals($id->value(), $user->id()->value());
        $this->assertEquals('Test User', $user->name());
        $this->assertEquals('test@example.com', $user->email()->value());
        $this->assertEquals('hashed_password', $user->password());
        $this->assertEquals($createdAt, $user->createdAt());
        $this->assertEquals($updatedAt, $user->updatedAt());
    }
}
