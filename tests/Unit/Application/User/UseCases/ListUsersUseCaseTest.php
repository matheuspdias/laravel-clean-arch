<?php

namespace Tests\Unit\Application\User\UseCases;

use App\Application\User\DTOs\Request\ListUsersDTO;
use App\Application\User\UseCases\ListUsersUseCase;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use Mockery;
use PHPUnit\Framework\TestCase;

class ListUsersUseCaseTest extends TestCase
{
    private UserRepository $userRepository;
    private ListUsersUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->useCase = new ListUsersUseCase($this->userRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_should_list_users_successfully(): void
    {
        $user1 = User::create('John Doe', 'john@example.com', 'password123');
        $user2 = User::create('Jane Doe', 'jane@example.com', 'password456');

        $dto = new ListUsersDTO(1, 15);

        $this->userRepository
            ->shouldReceive('findAll')
            ->once()
            ->with(1, 15)
            ->andReturn([$user1, $user2]);

        $this->userRepository
            ->shouldReceive('count')
            ->once()
            ->andReturn(2);

        $output = $this->useCase->execute($dto);

        $this->assertCount(2, $output->users);
        $this->assertEquals(2, $output->total);
        $this->assertEquals(1, $output->page);
        $this->assertEquals(15, $output->perPage);
        $this->assertEquals(1, $output->totalPages());
    }

    public function test_should_calculate_total_pages_correctly(): void
    {
        $dto = new ListUsersDTO(1, 10);

        $this->userRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn([]);

        $this->userRepository
            ->shouldReceive('count')
            ->once()
            ->andReturn(25);

        $output = $this->useCase->execute($dto);

        $this->assertEquals(3, $output->totalPages()); // 25 / 10 = 3 páginas
    }

    public function test_should_return_empty_list_when_no_users(): void
    {
        $dto = new ListUsersDTO(1, 15);

        $this->userRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn([]);

        $this->userRepository
            ->shouldReceive('count')
            ->once()
            ->andReturn(0);

        $output = $this->useCase->execute($dto);

        $this->assertCount(0, $output->users);
        $this->assertEquals(0, $output->total);
    }
}
