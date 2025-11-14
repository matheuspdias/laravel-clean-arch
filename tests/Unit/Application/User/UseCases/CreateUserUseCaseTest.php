<?php

namespace Tests\Unit\Application\User\UseCases;

use App\Application\User\DTOs\Request\CreateUserDTO;
use App\Application\User\UseCases\CreateUserUseCase;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\Services\UserDomainService;
use App\Domain\User\ValueObjects\Email;
use Mockery;
use PHPUnit\Framework\TestCase;

class CreateUserUseCaseTest extends TestCase
{
    private UserRepository $userRepository;
    private UserDomainService $userDomainService;
    private CreateUserUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->userDomainService = Mockery::mock(UserDomainService::class);
        $this->useCase = new CreateUserUseCase(
            $this->userRepository,
            $this->userDomainService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_should_create_user_successfully(): void
    {
        $dto = new CreateUserDTO(
            'John Doe',
            'john@example.com',
            'password123'
        );

        $this->userDomainService
            ->shouldReceive('ensureEmailIsUnique')
            ->once()
            ->with(Mockery::on(function ($email) {
                return $email instanceof Email && $email->value() === 'john@example.com';
            }));

        $this->userRepository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::type(User::class));

        $output = $this->useCase->execute($dto);

        $this->assertEquals('John Doe', $output->name);
        $this->assertEquals('john@example.com', $output->email);
        $this->assertNotEmpty($output->id);
        $this->assertInstanceOf(\DateTimeImmutable::class, $output->createdAt);
    }

    public function test_should_throw_exception_when_email_already_exists(): void
    {
        $dto = new CreateUserDTO(
            'John Doe',
            'john@example.com',
            'password123'
        );

        $this->userDomainService
            ->shouldReceive('ensureEmailIsUnique')
            ->once()
            ->andThrow(new \DomainException('Email já está em uso'));

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Email já está em uso');

        $this->useCase->execute($dto);
    }

    public function test_should_hash_password(): void
    {
        $dto = new CreateUserDTO(
            'John Doe',
            'john@example.com',
            'password123'
        );

        $this->userDomainService
            ->shouldReceive('ensureEmailIsUnique')
            ->once();

        $savedUser = null;
        $this->userRepository
            ->shouldReceive('save')
            ->once()
            ->andReturnUsing(function (User $user) use (&$savedUser) {
                $savedUser = $user;
            });

        $this->useCase->execute($dto);

        $this->assertNotNull($savedUser);
        $this->assertTrue(password_verify('password123', $savedUser->password()));
    }
}
