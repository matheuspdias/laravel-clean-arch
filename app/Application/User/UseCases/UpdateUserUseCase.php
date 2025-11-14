<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTOs\Request\UpdateUserDTO;
use App\Application\User\DTOs\Response\UserDTO;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\Services\UserDomainService;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\UserId;

class UpdateUserUseCase
{
    private UserRepository $userRepository;
    private UserDomainService $userDomainService;

    public function __construct(
        UserRepository $userRepository,
        UserDomainService $userDomainService
    ) {
        $this->userRepository = $userRepository;
        $this->userDomainService = $userDomainService;
    }

    public function execute(UpdateUserDTO $dto): UserDTO
    {
        $userId = new UserId($dto->id);
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new \DomainException('Usuário não encontrado');
        }

        $newEmail = new Email($dto->email);
        $this->userDomainService->ensureEmailIsUniqueForUpdate($newEmail, $user);

        $user->updateProfile($dto->name, $dto->email);

        $this->userRepository->save($user);

        return new UserDTO(
            $user->id()->value(),
            $user->name(),
            $user->email()->value(),
            $user->createdAt(),
            $user->updatedAt()
        );
    }
}
