<?php

namespace Tests\Unit\Application\User\UseCases;

use App\Application\User\DTOs\Request\UpdateUserDTO;
use App\Application\User\UseCases\UpdateUserUseCase;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\Services\UserDomainService;
use App\Domain\User\ValueObjects\UserId;
use Mockery;
use PHPUnit\Framework\TestCase;

class UpdateUserUseCaseTest extends TestCase
{
    private UserRepository $userRepository;
    private UserDomainService $userDomainService;
    private UpdateUserUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->userDomainService = Mockery::mock(UserDomainService::class);
        $this->useCase = new UpdateUserUseCase(
            $this->userRepository,
            $this->userDomainService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_should_update_user_successfully(): void
    {
        $userId = UserId::generate();
        $existingUser = User::create('Old Name', 'old@example.com', 'password123');

        $dto = new UpdateUserDTO(
            $userId->value(),
            'New Name',
            'new@example.com'
        );

        $this->userRepository
            ->shouldReceive('findById')
            ->once()
            ->with(Mockery::on(fn($id) => $id->value() === $userId->value()))
            ->andReturn($existingUser);

        $this->userDomainService
            ->shouldReceive('ensureEmailIsUniqueForUpdate')
            ->once();

        $this->userRepository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::type(User::class));

        $output = $this->useCase->execute($dto);

        $this->assertEquals('New Name', $output->name);
        $this->assertEquals('new@example.com', $output->email);
    }

    public function test_should_throw_exception_when_user_not_found(): void
    {
        $dto = new UpdateUserDTO(
            UserId::generate()->value(),
            'New Name',
            'new@example.com'
        );

        $this->userRepository
            ->shouldReceive('findById')
            ->once()
            ->andReturn(null);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não encontrado');

        $this->useCase->execute($dto);
    }

    public function test_should_throw_exception_when_email_already_used_by_another_user(): void
    {
        $userId = UserId::generate();
        $existingUser = User::create('Old Name', 'old@example.com', 'password123');

        $dto = new UpdateUserDTO(
            $userId->value(),
            'New Name',
            'taken@example.com'
        );

        $this->userRepository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($existingUser);

        $this->userDomainService
            ->shouldReceive('ensureEmailIsUniqueForUpdate')
            ->once()
            ->andThrow(new \DomainException('Email já está em uso por outro usuário'));

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Email já está em uso por outro usuário');

        $this->useCase->execute($dto);
    }
}
