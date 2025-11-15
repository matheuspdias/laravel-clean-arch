<?php

namespace App\Infrastructure\Persistence\Eloquent\User;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\UserId;

class EloquentUserRepository implements UserRepository
{
    private UserModel $model;

    public function __construct(UserModel $model)
    {
        $this->model = $model;
    }

    public function save(User $user): void
    {
        $this->model->updateOrCreate(
            ['id' => $user->id()->value()],
            [
                'name' => $user->name(),
                'email' => $user->email()->value(),
                'password' => $user->password(),
                'created_at' => $user->createdAt(),
                'updated_at' => $user->updatedAt(),
            ]
        );
    }

    public function findById(UserId $id): ?User
    {
        $model = $this->model->find($id->value());

        if (!$model) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function findByEmail(Email $email): ?User
    {
        $model = $this->model->where('email', $email->value())->first();

        if (!$model) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function existsByEmail(Email $email): bool
    {
        return $this->model->where('email', $email->value())->exists();
    }

    public function delete(UserId $id): void
    {
        $this->model->where('id', $id->value())->delete();
    }

    public function findAll(int $page = 1, int $perPage = 15): array
    {
        $models = $this->model
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return $models->map(fn($model) => $this->toDomain($model))->toArray();
    }

    public function count(): int
    {
        return $this->model->count();
    }

    private function toDomain(UserModel $model): User
    {
        return new User(
            new UserId($model->id),
            $model->name,
            new Email($model->email),
            $model->password,
            \DateTimeImmutable::createFromMutable($model->created_at),
            \DateTimeImmutable::createFromMutable($model->updated_at)
        );
    }
}
