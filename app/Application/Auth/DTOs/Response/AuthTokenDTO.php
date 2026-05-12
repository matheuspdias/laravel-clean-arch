<?php

namespace App\Application\Auth\DTOs\Response;

use App\Application\User\DTOs\Response\UserDTO;

class AuthTokenDTO
{
    public function __construct(
        public readonly string $token,
        public readonly string $tokenType,
        public readonly UserDTO $user,
    ) {}
}
