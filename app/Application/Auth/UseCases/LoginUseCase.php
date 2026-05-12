<?php

namespace App\Application\Auth\UseCases;

use App\Application\Auth\Contracts\TokenService;
use App\Application\Auth\DTOs\Request\LoginDTO;
use App\Application\Auth\DTOs\Response\AuthTokenDTO;
use App\Application\User\DTOs\Response\UserDTO;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\ValueObjects\Email;

class LoginUseCase
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TokenService $tokenService,
    ) {}

    public function execute(LoginDTO $dto): AuthTokenDTO
    {
        $user = $this->userRepository->findByEmail(new Email($dto->email));

        if (!$user || !password_verify($dto->password, $user->password())) {
            throw new \DomainException('Credenciais inválidas');
        }

        $token = $this->tokenService->createToken($user);

        return new AuthTokenDTO(
            token: $token,
            tokenType: 'Bearer',
            user: new UserDTO(
                $user->id()->value(),
                $user->name(),
                $user->email()->value(),
                $user->createdAt(),
                $user->updatedAt(),
            ),
        );
    }
}
