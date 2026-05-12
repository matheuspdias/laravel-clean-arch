<?php

namespace Tests\Unit\Application\User\UseCases;

use App\Application\User\UseCases\DeleteUserUseCase;
use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\InMemoryUserRepository;

class DeleteUserUseCaseTest extends TestCase
{
    private InMemoryUserRepository $repository;
    private DeleteUserUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new InMemoryUserRepository();
        $this->useCase    = new DeleteUserUseCase($this->repository);
    }

    public function test_should_delete_user_successfully(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $this->useCase->execute($user->id()->value());

        $deleted = $this->repository->findById($user->id());
        $this->assertNull($deleted);
    }

    public function test_repository_is_empty_after_deletion(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $this->useCase->execute($user->id()->value());

        $this->assertEquals(0, $this->repository->count());
    }

    public function test_should_throw_when_user_not_found(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não encontrado');

        $this->useCase->execute(UserId::generate()->value());
    }

    public function test_should_only_delete_the_target_user(): void
    {
        $user1 = User::create('John Doe', 'john@example.com', 'password123');
        $user2 = User::create('Jane Doe', 'jane@example.com', 'password456');
        $this->repository->save($user1);
        $this->repository->save($user2);

        $this->useCase->execute($user1->id()->value());

        $this->assertNull($this->repository->findById($user1->id()));
        $this->assertNotNull($this->repository->findById($user2->id()));
    }
}
