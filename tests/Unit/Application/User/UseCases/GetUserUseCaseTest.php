<?php

namespace Tests\Unit\Application\User\UseCases;

use App\Application\User\UseCases\GetUserUseCase;
use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\InMemoryUserRepository;

class GetUserUseCaseTest extends TestCase
{
    private InMemoryUserRepository $repository;
    private GetUserUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new InMemoryUserRepository();
        $this->useCase    = new GetUserUseCase($this->repository);
    }

    public function test_should_return_user_by_id(): void
    {
        $user = User::create('John Doe', 'john@example.com', 'password123');
        $this->repository->save($user);

        $output = $this->useCase->execute($user->id()->value());

        $this->assertEquals($user->id()->value(), $output->id);
        $this->assertEquals($user->name(), $output->name);
        $this->assertEquals($user->email()->value(), $output->email);
        $this->assertInstanceOf(\DateTimeImmutable::class, $output->createdAt);
    }

    public function test_should_throw_when_user_not_found(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não encontrado');

        $this->useCase->execute(UserId::generate()->value());
    }

    public function test_should_return_correct_user_among_many(): void
    {
        $user1 = User::create('John Doe', 'john@example.com', 'password123');
        $user2 = User::create('Jane Doe', 'jane@example.com', 'password456');
        $this->repository->save($user1);
        $this->repository->save($user2);

        $output = $this->useCase->execute($user2->id()->value());

        $this->assertEquals($user2->id()->value(), $output->id);
        $this->assertEquals($user2->name(), $output->name);
    }
}
