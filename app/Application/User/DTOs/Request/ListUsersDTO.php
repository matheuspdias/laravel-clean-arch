<?php

namespace App\Application\User\DTOs\Request;

class ListUsersDTO
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 15
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            page: (int) ($data['page'] ?? 1),
            perPage: (int) ($data['per_page'] ?? 15)
        );
    }

    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];
    }
}
