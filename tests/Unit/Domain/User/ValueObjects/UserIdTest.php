<?php

namespace Tests\Unit\Domain\User\ValueObjects;

use App\Domain\User\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

class UserIdTest extends TestCase
{
    public function test_should_create_valid_user_id(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $userId = new UserId($uuid);

        $this->assertEquals($uuid, $userId->value());
    }

    public function test_should_generate_new_user_id(): void
    {
        $userId = UserId::generate();

        $this->assertNotEmpty($userId->value());
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $userId->value()
        );
    }

    public function test_should_throw_exception_for_invalid_uuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ID de usuário inválido');

        new UserId('invalid-uuid');
    }

    public function test_should_compare_user_ids_correctly(): void
    {
        $uuid1 = '550e8400-e29b-41d4-a716-446655440000';
        $uuid2 = '550e8400-e29b-41d4-a716-446655440000';
        $uuid3 = '550e8400-e29b-41d4-a716-446655440001';

        $userId1 = new UserId($uuid1);
        $userId2 = new UserId($uuid2);
        $userId3 = new UserId($uuid3);

        $this->assertTrue($userId1->equals($userId2));
        $this->assertFalse($userId1->equals($userId3));
    }

    public function test_should_convert_to_string(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $userId = new UserId($uuid);

        $this->assertEquals($uuid, (string) $userId);
    }
}
