<?php

namespace App\Domain\User\Entities;

use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\UserId;
use DateTimeImmutable;

class User
{
    private UserId $id;
    private string $name;
    private Email $email;
    private string $password;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        UserId $id,
        string $name,
        Email $email,
        string $password,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->setName($name);
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function create(
        string $name,
        string $email,
        string $password
    ): self {
        return new self(
            UserId::generate(),
            $name,
            new Email($email),
            password_hash($password, PASSWORD_BCRYPT),
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );
    }

    public function updateProfile(string $name, string $email): void
    {
        $this->setName($name);
        $this->email = new Email($email);
        $this->updatedAt = new DateTimeImmutable();
    }

    public function changePassword(string $newPassword): void
    {
        $this->password = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->updatedAt = new DateTimeImmutable();
    }

    private function setName(string $name): void
    {
        if (empty(trim($name))) {
            throw new \InvalidArgumentException('Nome não pode ser vazio');
        }

        if (strlen($name) < 3) {
            throw new \InvalidArgumentException('Nome deve ter no mínimo 3 caracteres');
        }

        $this->name = $name;
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
