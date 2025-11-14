<?php

namespace Tests\Unit\Application\User\UseCases;

use App\Application\User\UseCases\DeleteUserUseCase;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\ValueObjects\UserId;
use Mockery;
use PHPUnit\Framework\TestCase;

class DeleteUserUseCaseTest extends TestCase
{
    private UserRepository $userRepository;
    private DeleteUserUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->useCase = new DeleteUserUseCase($this->userRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_should_delete_user_successfully(): void
    {
        $userId = UserId::generate();
        $user = User::create('John Doe', 'john@example.com', 'password123');

        $this->userRepository
            ->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn($id) => $id->value() === $userId->value()))
            ->andReturn($user);

        $this->userRepository
            ->shouldReceive('delete')
            ->once()
            ->with(Mockery::on(fn($id) => $id->value() === $userId->value()));

        $this->useCase->execute($userId->value());

        $this->assertTrue(true); // Se chegou aqui, o teste passou
    }

    public function test_should_throw_exception_when_user_not_found(): void
    {
        $userId = UserId::generate();

        $this->userRepository
            ->shouldReceive('findById')
            ->once()
            ->andReturn(null);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não encontrado');

        $this->useCase->execute($userId->value());
    }
}
