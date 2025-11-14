<?php

namespace Tests\Feature\Api;

use App\Domain\User\Entities\User;
use App\Infrastructure\Persistence\Eloquent\EloquentUserRepository;
use App\Infrastructure\Persistence\Eloquent\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    private EloquentUserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentUserRepository(new UserModel());
    }

    public function test_should_create_user_via_api(): void
    {
        $response = $this->postJson('/api/v1/users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->assertJson([
                'message' => 'Usuário criado com sucesso',
                'data' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_should_fail_to_create_user_with_invalid_data(): void
    {
        $response = $this->postJson('/api/v1/users', [
            'name' => 'Jo', // Muito curto
            'email' => 'invalid-email',
            'password' => '123', // Muito curta
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_should_fail_to_create_user_with_duplicate_email(): void
    {
        $user = User::create('Jane Doe', 'duplicate@example.com', 'password123');
        $this->repository->save($user);

        $response = $this->postJson('/api/v1/users', [
            'name' => 'John Doe',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Email já está em uso',
            ]);
    }

    public function test_should_list_users_via_api(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $user = User::create("User {$i}", "user{$i}@example.com", 'password123');
            $this->repository->save($user);
        }

        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'meta' => [
                    'total',
                    'page',
                    'per_page',
                    'total_pages',
                ]
            ])
            ->assertJsonCount(3, 'data')
            ->assertJson([
                'meta' => [
                    'total' => 3,
                    'page' => 1,
                    'per_page' => 15,
                ]
            ]);
    }

    public function test_should_paginate_users(): void
    {
        for ($i = 1; $i <= 25; $i++) {
            $user = User::create("User {$i}", "user{$i}@example.com", 'password123');
            $this->repository->save($user);
        }

        $response = $this->getJson('/api/v1/users?page=2&per_page=10');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJson([
                'meta' => [
                    'total' => 25,
                    'page' => 2,
                    'per_page' => 10,
                    'total_pages' => 3,
                ]
            ]);
    }

    public function test_should_get_user_by_id_via_api(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $response = $this->getJson("/api/v1/users/{$user->id()->value()}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $user->id()->value(),
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                ]
            ]);
    }

    public function test_should_return_404_when_user_not_found(): void
    {
        $response = $this->getJson('/api/v1/users/550e8400-e29b-41d4-a716-446655440000');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Usuário não encontrado',
            ]);
    }

    public function test_should_update_user_via_api(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $response = $this->putJson("/api/v1/users/{$user->id()->value()}", [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Usuário atualizado com sucesso',
                'data' => [
                    'id' => $user->id()->value(),
                    'name' => 'Jane Doe',
                    'email' => 'jane@example.com',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id()->value(),
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);
    }

    public function test_should_fail_to_update_user_with_invalid_data(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $response = $this->putJson("/api/v1/users/{$user->id()->value()}", [
            'name' => 'J',
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);
    }

    public function test_should_fail_to_update_user_with_email_already_in_use(): void
    {
        $user1 = User::create('User 1', 'user1@example.com', 'password123');
        $user2 = User::create('User 2', 'user2@example.com', 'password123');
        $this->repository->save($user1);
        $this->repository->save($user2);

        $response = $this->putJson("/api/v1/users/{$user1->id()->value()}", [
            'name' => 'User 1',
            'email' => 'user2@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Email já está em uso por outro usuário',
            ]);
    }

    public function test_should_delete_user_via_api(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $response = $this->deleteJson("/api/v1/users/{$user->id()->value()}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Usuário deletado com sucesso',
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id()->value(),
        ]);
    }

    public function test_should_return_404_when_deleting_nonexistent_user(): void
    {
        $response = $this->deleteJson('/api/v1/users/550e8400-e29b-41d4-a716-446655440000');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Usuário não encontrado',
            ]);
    }

    public function test_should_not_expose_password_in_response(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $response = $this->getJson("/api/v1/users/{$user->id()->value()}");

        $response->assertStatus(200)
            ->assertJsonMissing(['password']);
    }
}
