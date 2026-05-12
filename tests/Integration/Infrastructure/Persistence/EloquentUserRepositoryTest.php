<?php

namespace Tests\Integration\Infrastructure\Persistence;

use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\UserId;
use App\Infrastructure\Persistence\Eloquent\User\EloquentUserRepository;
use App\Infrastructure\Persistence\Eloquent\User\UserModel;
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
            'id'    => $user->id()->value(),
            'name'  => $user->name(),
            'email' => $user->email()->value(),
        ]);
    }

    public function test_should_update_existing_user(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $user->updateProfile('Jane Doe', 'jane@example.com');
        $this->repository->save($user);

        $this->assertDatabaseHas('users', [
            'id'    => $user->id()->value(),
            'name'  => $user->name(),
            'email' => $user->email()->value(),
        ]);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_should_find_user_by_id(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $found = $this->repository->findById($user->id());

        $this->assertNotNull($found);
        $this->assertEquals($user->id()->value(), $found->id()->value());
        $this->assertEquals($user->name(), $found->name());
        $this->assertEquals($user->email()->value(), $found->email()->value());
    }

    public function test_should_return_null_when_user_not_found_by_id(): void
    {
        $found = $this->repository->findById(UserId::generate());

        $this->assertNull($found);
    }

    public function test_should_find_user_by_email(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $found = $this->repository->findByEmail($user->email());

        $this->assertNotNull($found);
        $this->assertEquals($user->email()->value(), $found->email()->value());
    }

    public function test_should_return_null_when_user_not_found_by_email(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');

        $found = $this->repository->findByEmail($user->email());

        $this->assertNull($found);
    }

    public function test_should_check_if_email_exists(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $this->assertTrue($this->repository->existsByEmail($user->email()));
    }

    public function test_should_return_false_when_email_does_not_exist(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');

        $this->assertFalse($this->repository->existsByEmail($user->email()));
    }

    public function test_should_delete_user(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $this->repository->delete($user->id());

        $this->assertDatabaseMissing('users', ['id' => $user->id()->value()]);
    }

    public function test_should_find_all_users_with_pagination(): void
    {
        $totalUsers = 25;
        $perPage    = 10;

        for ($i = 1; $i <= $totalUsers; $i++) {
            $this->repository->save(User::create("User {$i}", "user{$i}@example.com", 'password123'));
        }

        $users = $this->repository->findAll(1, $perPage);

        $this->assertCount($perPage, $users);
    }

    public function test_should_find_second_page_of_users(): void
    {
        $totalUsers = 25;
        $perPage    = 10;

        for ($i = 1; $i <= $totalUsers; $i++) {
            $this->repository->save(User::create("User {$i}", "user{$i}@example.com", 'password123'));
        }

        $page1 = $this->repository->findAll(1, $perPage);
        $page2 = $this->repository->findAll(2, $perPage);

        $this->assertCount($perPage, $page2);

        $ids1 = array_map(fn(User $u) => $u->id()->value(), $page1);
        $ids2 = array_map(fn(User $u) => $u->id()->value(), $page2);
        $this->assertEmpty(array_intersect($ids1, $ids2));
    }

    public function test_should_count_users(): void
    {
        $totalUsers = 5;

        for ($i = 1; $i <= $totalUsers; $i++) {
            $this->repository->save(User::create("User {$i}", "user{$i}@example.com", 'password123'));
        }

        $this->assertEquals($totalUsers, $this->repository->count());
    }

    public function test_should_return_empty_array_when_no_users(): void
    {
        $this->assertCount(0, $this->repository->findAll(1, 10));
    }
}
