<?php

namespace Tests\Doubles;

use App\Application\Auth\Contracts\TokenService;
use App\Domain\User\Entities\User;

class InMemoryTokenService implements TokenService
{
    private array $tokens = [];

    public function createToken(User $user): string
    {
        $token = 'fake-token-' . $user->id()->value();
        $this->tokens[$token] = $user->id()->value();
        return $token;
    }

    public function revokeToken(string $plainTextToken): void
    {
        unset($this->tokens[$plainTextToken]);
    }

    public function isValid(string $plainTextToken): bool
    {
        return isset($this->tokens[$plainTextToken]);
    }

    public function tokenCount(): int
    {
        return count($this->tokens);
    }
}
