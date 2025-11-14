<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTOs\Request\CreateUserDTO;
use App\Application\User\DTOs\Response\UserDTO;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\Services\UserDomainService;
use App\Domain\User\ValueObjects\Email;

class CreateUserUseCase
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

    public function execute(CreateUserDTO $dto): UserDTO
    {
        $email = new Email($dto->email);

        $this->userDomainService->ensureEmailIsUnique($email);

        $user = User::create(
            $dto->name,
            $dto->email,
            $dto->password
        );

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
