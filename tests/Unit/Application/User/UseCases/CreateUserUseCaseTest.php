<?php

namespace Tests\Unit\Application\User\UseCases;

use App\Application\User\DTOs\Request\CreateUserDTO;
use App\Application\User\UseCases\CreateUserUseCase;
use App\Domain\User\Services\UserDomainService;
use App\Domain\User\ValueObjects\Email;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\InMemoryUserRepository;

class CreateUserUseCaseTest extends TestCase
{
    private InMemoryUserRepository $repository;
    private CreateUserUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new InMemoryUserRepository();
        $domainService    = new UserDomainService($this->repository);
        $this->useCase    = new CreateUserUseCase($this->repository, $domainService);
    }

    public function test_should_create_user_successfully(): void
    {
        $dto = new CreateUserDTO('John Doe', 'john@example.com', 'password123');

        $output = $this->useCase->execute($dto);

        $this->assertEquals($dto->name, $output->name);
        $this->assertEquals($dto->email, $output->email);
        $this->assertNotEmpty($output->id);
        $this->assertInstanceOf(\DateTimeImmutable::class, $output->createdAt);
    }

    public function test_user_is_persisted_in_repository(): void
    {
        $dto = new CreateUserDTO('John Doe', 'john@example.com', 'password123');

        $output = $this->useCase->execute($dto);

        $saved = $this->repository->findByEmail(new Email($dto->email));
        $this->assertNotNull($saved);
        $this->assertEquals($output->id, $saved->id()->value());
    }

    public function test_should_hash_password(): void
    {
        $dto = new CreateUserDTO('John Doe', 'john@example.com', 'password123');

        $this->useCase->execute($dto);

        $saved = $this->repository->findByEmail(new Email($dto->email));
        $this->assertTrue(password_verify($dto->password, $saved->password()));
    }

    public function test_should_throw_when_email_already_exists(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Email já está em uso');

        $dto = new CreateUserDTO('John Doe', 'john@example.com', 'password123');
        $this->useCase->execute($dto);
        $this->useCase->execute($dto);
    }

    public function test_repository_contains_one_user_after_creation(): void
    {
        $dto = new CreateUserDTO('John Doe', 'john@example.com', 'password123');

        $this->useCase->execute($dto);

        $this->assertEquals(1, $this->repository->count());
    }
}
