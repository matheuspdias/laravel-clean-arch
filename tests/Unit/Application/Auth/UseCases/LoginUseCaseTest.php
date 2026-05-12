<?php

namespace Tests\Unit\Application\Auth\UseCases;

use App\Application\Auth\DTOs\Request\LoginDTO;
use App\Application\Auth\DTOs\Response\AuthTokenDTO;
use App\Application\Auth\UseCases\LoginUseCase;
use App\Domain\User\Entities\User;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\InMemoryTokenService;
use Tests\Doubles\InMemoryUserRepository;

class LoginUseCaseTest extends TestCase
{
    private InMemoryUserRepository $repository;
    private InMemoryTokenService $tokenService;
    private LoginUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository   = new InMemoryUserRepository();
        $this->tokenService = new InMemoryTokenService();
        $this->useCase      = new LoginUseCase($this->repository, $this->tokenService);
    }

    public function test_should_login_with_valid_credentials(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $dto    = new LoginDTO('john@example.com', 'password123');
        $output = $this->useCase->execute($dto);

        $this->assertInstanceOf(AuthTokenDTO::class, $output);
        $this->assertNotEmpty($output->token);
        $this->assertEquals('Bearer', $output->tokenType);
        $this->assertEquals($user->id()->value(), $output->user->id);
        $this->assertEquals($user->email()->value(), $output->user->email);
    }

    public function test_token_is_stored_after_login(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $dto    = new LoginDTO('john@example.com', 'password123');
        $output = $this->useCase->execute($dto);

        $this->assertTrue($this->tokenService->isValid($output->token));
        $this->assertEquals(1, $this->tokenService->tokenCount());
    }

    public function test_should_throw_when_email_not_found(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Credenciais inválidas');

        $this->useCase->execute(new LoginDTO('notfound@example.com', 'password123'));
    }

    public function test_should_throw_when_password_is_wrong(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Credenciais inválidas');

        $this->useCase->execute(new LoginDTO($user->email()->value(), 'wrong-password'));
    }

    public function test_should_not_reveal_if_email_exists_on_wrong_password(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        try {
            $this->useCase->execute(new LoginDTO($user->email()->value(), 'wrong'));
            $this->fail('Expected DomainException');
        } catch (\DomainException $e) {
            $this->assertEquals('Credenciais inválidas', $e->getMessage());
        }

        try {
            $this->useCase->execute(new LoginDTO('notfound@example.com', 'wrong'));
            $this->fail('Expected DomainException');
        } catch (\DomainException $e) {
            $this->assertEquals('Credenciais inválidas', $e->getMessage());
        }
    }
}
