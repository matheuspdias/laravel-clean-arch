<?php

namespace Tests\Integration\Infrastructure\Persistence;

use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\UserId;
use App\Infrastructure\Persistence\Eloquent\EloquentUserRepository;
use App\Infrastructure\Persistence\Eloquent\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentUserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentUserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentUserRepository(new UserModel());
    }

    public function test_should_save_new_user(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');

        $this->repository->save($user);

        $this->assertDatabaseHas('users', [
            'id' => $user->id()->value(),
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_should_update_existing_user(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $user->updateProfile('Jane Doe', 'jane@example.com');
        $this->repository->save($user);

        $this->assertDatabaseHas('users', [
            'id' => $user->id()->value(),
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_should_find_user_by_id(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $foundUser = $this->repository->findById($user->id());

        $this->assertNotNull($foundUser);
        $this->assertEquals($user->id()->value(), $foundUser->id()->value());
        $this->assertEquals('John Doe', $foundUser->name());
        $this->assertEquals('john@example.com', $foundUser->email()->value());
    }

    public function test_should_return_null_when_user_not_found_by_id(): void
    {
        $userId = UserId::generate();

        $foundUser = $this->repository->findById($userId);

        $this->assertNull($foundUser);
    }

    public function test_should_find_user_by_email(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $email = new Email('john@example.com');
        $foundUser = $this->repository->findByEmail($email);

        $this->assertNotNull($foundUser);
        $this->assertEquals('john@example.com', $foundUser->email()->value());
    }

    public function test_should_return_null_when_user_not_found_by_email(): void
    {
        $email = new Email('nonexistent@example.com');

        $foundUser = $this->repository->findByEmail($email);

        $this->assertNull($foundUser);
    }

    public function test_should_check_if_email_exists(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $email = new Email('john@example.com');
        $exists = $this->repository->existsByEmail($email);

        $this->assertTrue($exists);
    }

    public function test_should_return_false_when_email_does_not_exist(): void
    {
        $email = new Email('nonexistent@example.com');

        $exists = $this->repository->existsByEmail($email);

        $this->assertFalse($exists);
    }

    public function test_should_delete_user(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $this->repository->delete($user->id());

        $this->assertDatabaseMissing('users', [
            'id' => $user->id()->value(),
        ]);
    }

    public function test_should_find_all_users_with_pagination(): void
    {
        for ($i = 1; $i <= 25; $i++) {
            $user = User::create("User {$i}", "user{$i}@example.com", 'password123');
            $this->repository->save($user);
        }

        $users = $this->repository->findAll(1, 10);

        $this->assertCount(10, $users);
    }

    public function test_should_find_second_page_of_users(): void
    {
        for ($i = 1; $i <= 25; $i++) {
            $user = User::create("User {$i}", "user{$i}@example.com", 'password123');
            $this->repository->save($user);
        }

        $page2Users = $this->repository->findAll(2, 10);

        $this->assertCount(10, $page2Users);
    }

    public function test_should_count_users(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create("User {$i}", "user{$i}@example.com", 'password123');
            $this->repository->save($user);
        }

        $count = $this->repository->count();

        $this->assertEquals(5, $count);
    }

    public function test_should_return_empty_array_when_no_users(): void
    {
        $users = $this->repository->findAll(1, 10);

        $this->assertCount(0, $users);
    }
}
