<?php

namespace App\Infrastructure\Auth;

use App\Application\Auth\Contracts\TokenService;
use App\Domain\User\Entities\User;
use App\Infrastructure\Persistence\Eloquent\User\UserModel;
use Laravel\Sanctum\PersonalAccessToken;

class SanctumTokenService implements TokenService
{
    public function createToken(User $user): string
    {
        $model = UserModel::findOrFail($user->id()->value());

        return $model->createToken('auth_token')->plainTextToken;
    }

    public function revokeToken(string $plainTextToken): void
    {
        PersonalAccessToken::findToken($plainTextToken)?->delete();
    }
}
