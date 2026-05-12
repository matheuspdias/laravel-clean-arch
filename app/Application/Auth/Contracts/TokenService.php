<?php

namespace App\Application\Auth\Contracts;

use App\Domain\User\Entities\User;

interface TokenService
{
    public function createToken(User $user): string;

    public function revokeToken(string $plainTextToken): void;
}
