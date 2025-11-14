<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTOs\Request\ListUsersDTO;
use App\Application\User\DTOs\Response\UserDTO;
use App\Application\User\DTOs\Response\UserListDTO;
use App\Domain\User\Repositories\UserRepository;

class ListUsersUseCase
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(ListUsersDTO $dto): UserListDTO
    {
        $users = $this->userRepository->findAll($dto->page, $dto->perPage);
        $total = $this->userRepository->count();

        $usersDTO = array_map(function ($user) {
            return new UserDTO(
                $user->id()->value(),
                $user->name(),
                $user->email()->value(),
                $user->createdAt(),
                $user->updatedAt()
            );
        }, $users);

        return new UserListDTO(
            $usersDTO,
            $total,
            $dto->page,
            $dto->perPage
        );
    }
}
