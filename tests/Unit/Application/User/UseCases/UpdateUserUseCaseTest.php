<?php

namespace Tests\Unit\Application\User\UseCases;

use App\Application\User\DTOs\Request\UpdateUserDTO;
use App\Application\User\UseCases\UpdateUserUseCase;
use App\Domain\User\Entities\User;
use App\Domain\User\Services\UserDomainService;
use App\Domain\User\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\InMemoryUserRepository;

class UpdateUserUseCaseTest extends TestCase
{
    private InMemoryUserRepository $repository;
    private UpdateUserUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new InMemoryUserRepository();
        $domainService    = new UserDomainService($this->repository);
        $this->useCase    = new UpdateUserUseCase($this->repository, $domainService);
    }

    public function test_should_update_user_successfully(): void
    {
        $user = User::create('Old Name', 'old@example.com', 'password123');
        $this->repository->save($user);

        $dto    = new UpdateUserDTO($user->id()->value(), 'New Name', 'new@example.com');
        $output = $this->useCase->execute($dto);

        $this->assertEquals($dto->name, $output->name);
        $this->assertEquals($dto->email, $output->email);
    }

    public function test_updated_data_is_persisted_in_repository(): void
    {
        $user = User::create('Old Name', 'old@example.com', 'password123');
        $this->repository->save($user);

        $dto = new UpdateUserDTO($user->id()->value(), 'New Name', 'new@example.com');
        $this->useCase->execute($dto);

        $persisted = $this->repository->findById($user->id());
        $this->assertEquals($dto->name, $persisted->name());
        $this->assertEquals($dto->email, $persisted->email()->value());
    }

    public function test_should_allow_keeping_same_email(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $dto    = new UpdateUserDTO($user->id()->value(), 'John Updated', $user->email()->value());
        $output = $this->useCase->execute($dto);

        $this->assertEquals($dto->name, $output->name);
        $this->assertEquals($dto->email, $output->email);
    }

    public function test_should_throw_when_user_not_found(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não encontrado');

        $dto = new UpdateUserDTO(UserId::generate()->value(), 'Name', 'email@example.com');
        $this->useCase->execute($dto);
    }

    public function test_should_throw_when_email_is_taken_by_another_user(): void
    {
        $user1 = User::create('User One', 'one@example.com', 'password123');
        $user2 = User::create('User Two', 'two@example.com', 'password456');
        $this->repository->save($user1);
        $this->repository->save($user2);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Email já está em uso por outro usuário');

        $dto = new UpdateUserDTO($user2->id()->value(), 'User Two', 'one@example.com');
        $this->useCase->execute($dto);
    }
}
