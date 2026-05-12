<?php

namespace Tests\Doubles;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\UserId;

class InMemoryUserRepository implements UserRepository
{
    /** @var User[] */
    private array $users = [];

    public function save(User $user): void
    {
        $this->users[$user->id()->value()] = $user;
    }

    public function findById(UserId $id): ?User
    {
        return $this->users[$id->value()] ?? null;
    }

    public function findByEmail(Email $email): ?User
    {
        foreach ($this->users as $user) {
            if ($user->email()->equals($email)) {
                return $user;
            }
        }

        return null;
    }

    public function existsByEmail(Email $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    public function delete(UserId $id): void
    {
        unset($this->users[$id->value()]);
    }

    public function findAll(int $page = 1, int $perPage = 15): array
    {
        $sorted = array_values($this->users);

        usort($sorted, fn(User $a, User $b) => $b->createdAt() <=> $a->createdAt());

        return array_slice($sorted, ($page - 1) * $perPage, $perPage);
    }

    public function count(): int
    {
        return count($this->users);
    }
}
