<?php

namespace Tests\Unit\Application\Auth\UseCases;

use App\Application\Auth\UseCases\LogoutUseCase;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\InMemoryTokenService;
use Tests\Doubles\InMemoryUserRepository;
use App\Domain\User\Entities\User;
use App\Application\Auth\UseCases\LoginUseCase;
use App\Application\Auth\DTOs\Request\LoginDTO;

class LogoutUseCaseTest extends TestCase
{
    private InMemoryTokenService $tokenService;
    private LogoutUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tokenService = new InMemoryTokenService();
        $this->useCase      = new LogoutUseCase($this->tokenService);
    }

    public function test_should_revoke_token_on_logout(): void
    {
        $repository = new InMemoryUserRepository();
        $user       = User::create('John Doe', 'john@example.com', 'password123');
        $repository->save($user);

        $loginUseCase = new LoginUseCase($repository, $this->tokenService);
        $output       = $loginUseCase->execute(new LoginDTO($user->email()->value(), 'password123'));

        $this->assertTrue($this->tokenService->isValid($output->token));

        $this->useCase->execute($output->token);

        $this->assertFalse($this->tokenService->isValid($output->token));
    }

    public function test_repository_is_empty_after_logout(): void
    {
        $repository = new InMemoryUserRepository();
        $user       = User::create('John Doe', 'john@example.com', 'password123');
        $repository->save($user);

        $loginUseCase = new LoginUseCase($repository, $this->tokenService);
        $output       = $loginUseCase->execute(new LoginDTO($user->email()->value(), 'password123'));

        $this->assertEquals(1, $this->tokenService->tokenCount());

        $this->useCase->execute($output->token);

        $this->assertEquals(0, $this->tokenService->tokenCount());
    }

    public function test_should_only_revoke_the_given_token(): void
    {
        $repository = new InMemoryUserRepository();
        $user1      = User::create('John Doe', 'john@example.com', 'password123');
        $user2      = User::create('Jane Doe', 'jane@example.com', 'password456');
        $repository->save($user1);
        $repository->save($user2);

        $loginUseCase = new LoginUseCase($repository, $this->tokenService);
        $output1      = $loginUseCase->execute(new LoginDTO($user1->email()->value(), 'password123'));
        $output2      = $loginUseCase->execute(new LoginDTO($user2->email()->value(), 'password456'));

        $this->useCase->execute($output1->token);

        $this->assertFalse($this->tokenService->isValid($output1->token));
        $this->assertTrue($this->tokenService->isValid($output2->token));
    }
}
