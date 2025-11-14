<?php

namespace App\Domain\User\Services;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\ValueObjects\Email;

class UserDomainService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function ensureEmailIsUnique(Email $email): void
    {
        if ($this->userRepository->existsByEmail($email)) {
            throw new \DomainException('Email já está em uso');
        }
    }

    public function ensureEmailIsUniqueForUpdate(Email $email, User $user): void
    {
        $existingUser = $this->userRepository->findByEmail($email);

        if ($existingUser && !$existingUser->id()->equals($user->id())) {
            throw new \DomainException('Email já está em uso por outro usuário');
        }
    }
}
