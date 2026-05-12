<?php

namespace App\Application\Auth\UseCases;

use App\Application\Auth\Contracts\TokenService;

class LogoutUseCase
{
    public function __construct(
        private readonly TokenService $tokenService,
    ) {}

    public function execute(string $plainTextToken): void
    {
        $this->tokenService->revokeToken($plainTextToken);
    }
}
