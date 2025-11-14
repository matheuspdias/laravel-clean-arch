<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\ValueObjects\UserId;

class DeleteUserUseCase
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(string $id): void
    {
        $userId = new UserId($id);
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new \DomainException('Usuário não encontrado');
        }

        $this->userRepository->delete($userId);
    }
}
