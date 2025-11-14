<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTOs\Response\UserDTO;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\ValueObjects\UserId;

class GetUserUseCase
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(string $id): UserDTO
    {
        $userId = new UserId($id);
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new \DomainException('Usuário não encontrado');
        }

        return new UserDTO(
            $user->id()->value(),
            $user->name(),
            $user->email()->value(),
            $user->createdAt(),
            $user->updatedAt()
        );
    }
}
