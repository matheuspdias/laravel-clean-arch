<?php

namespace App\Application\User\DTOs\Request;

class UpdateUserDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email
    ) {
    }

    public static function fromArray(string $id, array $data): self
    {
        return new self(
            id: $id,
            name: $data['name'],
            email: $data['email']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
