<?php

namespace App\Application\User\DTOs\Response;

class UserListDTO
{
    /**
     * @param UserDTO[] $users
     */
    public function __construct(
        public readonly array $users,
        public readonly int $total,
        public readonly int $page,
        public readonly int $perPage
    ) {
    }

    public function totalPages(): int
    {
        return (int) ceil($this->total / $this->perPage);
    }

    public function toArray(): array
    {
        return [
            'users' => array_map(fn(UserDTO $user) => $user->toArray(), $this->users),
            'total' => $this->total,
            'page' => $this->page,
            'per_page' => $this->perPage,
            'total_pages' => $this->totalPages(),
        ];
    }
}
