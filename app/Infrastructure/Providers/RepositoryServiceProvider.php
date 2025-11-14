<?php

namespace App\Infrastructure\Providers;

use App\Domain\User\Repositories\UserRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepository::class,
            EloquentUserRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
