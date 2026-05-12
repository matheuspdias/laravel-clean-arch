<?php

namespace Tests\Feature\Api;

use App\Domain\User\Entities\User;
use App\Infrastructure\Persistence\Eloquent\User\EloquentUserRepository;
use App\Infrastructure\Persistence\Eloquent\User\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    private EloquentUserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentUserRepository(new UserModel());
    }

    // ---------------------------------------------------------------
    // Login
    // ---------------------------------------------------------------

    public function test_should_login_with_valid_credentials(): void
    {
        $user    = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email()->value(),
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'token',
                    'token_type',
                    'user' => ['id', 'name', 'email', 'created_at', 'updated_at'],
                ],
            ])
            ->assertJson([
                'message' => 'Login realizado com sucesso',
                'data'    => [
                    'token_type' => 'Bearer',
                    'user'       => [
                        'id'    => $user->id()->value(),
                        'name'  => $user->name(),
                        'email' => $user->email()->value(),
                    ],
                ],
            ]);
    }

    public function test_should_return_token_on_login(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email()->value(),
            'password' => 'password123',
        ]);

        $token = $response->json('data.token');
        $this->assertNotEmpty($token);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id()->value(),
        ]);
    }

    public function test_should_fail_login_with_wrong_password(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email()->value(),
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Credenciais inválidas']);
    }

    public function test_should_fail_login_with_nonexistent_email(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'notfound@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Credenciais inválidas']);
    }

    public function test_should_fail_login_with_invalid_data(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'not-an-email',
            'password' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    // ---------------------------------------------------------------
    // Logout
    // ---------------------------------------------------------------

    public function test_should_logout_successfully(): void
    {
        $user  = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $token = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email()->value(),
            'password' => 'password123',
        ])->json('data.token');

        $response = $this->withToken($token)->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logout realizado com sucesso']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_should_reject_request_without_token_on_logout(): void
    {
        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(401);
    }

    // ---------------------------------------------------------------
    // Me
    // ---------------------------------------------------------------

    public function test_should_return_authenticated_user(): void
    {
        $user  = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $token = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email()->value(),
            'password' => 'password123',
        ])->json('data.token');

        $response = $this->withToken($token)->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email', 'created_at', 'updated_at'],
            ])
            ->assertJson([
                'data' => [
                    'id'    => $user->id()->value(),
                    'name'  => $user->name(),
                    'email' => $user->email()->value(),
                ],
            ]);
    }

    public function test_should_reject_request_without_token_on_me(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }

    public function test_should_not_expose_password_in_me_response(): void
    {
        $user  = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $token = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email()->value(),
            'password' => 'password123',
        ])->json('data.token');

        $response = $this->withToken($token)->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonMissing(['password']);
    }

    public function test_token_is_invalidated_after_logout(): void
    {
        $user  = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $token = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email()->value(),
            'password' => 'password123',
        ])->json('data.token');

        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertStatus(200);

        // The auth guard caches the resolved user in memory during the same test process.
        // Forgetting guards forces Sanctum to re-validate the token against the DB on the next request.
        $this->app['auth']->forgetGuards();

        $this->withToken($token)->getJson('/api/v1/auth/me')->assertStatus(401);
    }
}
