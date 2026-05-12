<?php

namespace Tests\Unit\Application\User\UseCases;

use App\Application\User\DTOs\Request\ListUsersDTO;
use App\Application\User\UseCases\ListUsersUseCase;
use App\Domain\User\Entities\User;
use PHPUnit\Framework\TestCase;
use Tests\Doubles\InMemoryUserRepository;

class ListUsersUseCaseTest extends TestCase
{
    private InMemoryUserRepository $repository;
    private ListUsersUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new InMemoryUserRepository();
        $this->useCase    = new ListUsersUseCase($this->repository);
    }

    public function test_should_return_empty_list_when_no_users(): void
    {
        $output = $this->useCase->execute(new ListUsersDTO(1, 15));

        $this->assertCount(0, $output->users);
        $this->assertEquals(0, $output->total);
    }

    public function test_should_list_all_users(): void
    {
        $this->repository->save(User::create('John Doe', 'john@example.com', 'password123'));
        $this->repository->save(User::create('Jane Doe', 'jane@example.com', 'password456'));

        $output = $this->useCase->execute(new ListUsersDTO(1, 15));

        $this->assertCount(2, $output->users);
        $this->assertEquals(2, $output->total);
    }

    public function test_should_paginate_correctly(): void
    {
        $totalUsers = 5;
        for ($i = 1; $i <= $totalUsers; $i++) {
            $this->repository->save(User::create("User $i", "user$i@example.com", 'password'));
        }

        $dto    = new ListUsersDTO(page: 1, perPage: 2);
        $output = $this->useCase->execute($dto);

        $this->assertCount($dto->perPage, $output->users);
        $this->assertEquals($totalUsers, $output->total);
        $this->assertEquals($dto->page, $output->page);
        $this->assertEquals($dto->perPage, $output->perPage);
    }

    public function test_should_calculate_total_pages_correctly(): void
    {
        $totalUsers = 25;
        for ($i = 1; $i <= $totalUsers; $i++) {
            $this->repository->save(User::create("User $i", "user$i@example.com", 'password'));
        }

        $dto    = new ListUsersDTO(page: 1, perPage: 10);
        $output = $this->useCase->execute($dto);

        $this->assertEquals((int) ceil($totalUsers / $dto->perPage), $output->totalPages());
    }

    public function test_second_page_returns_different_users(): void
    {
        $perPage = 2;
        for ($i = 1; $i <= 4; $i++) {
            $this->repository->save(User::create("User $i", "user$i@example.com", 'password'));
        }

        $dto1  = new ListUsersDTO(page: 1, perPage: $perPage);
        $dto2  = new ListUsersDTO(page: 2, perPage: $perPage);
        $page1 = $this->useCase->execute($dto1);
        $page2 = $this->useCase->execute($dto2);

        $ids1 = array_map(fn($u) => $u->id, $page1->users);
        $ids2 = array_map(fn($u) => $u->id, $page2->users);

        $this->assertCount($dto1->perPage, $page1->users);
        $this->assertCount($dto2->perPage, $page2->users);
        $this->assertEmpty(array_intersect($ids1, $ids2));
    }
}
