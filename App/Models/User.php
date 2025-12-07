<?php

namespace App\Models;

use Framework\Core\IIdentity;
use Framework\Core\Model;

/**
 * Class User
 *
 * Represents an application user stored in the `users` table and
 * acts as an identity implementation for authentication purposes.
 */
class User extends Model implements IIdentity
{
    /**
     * Explicit DB mapping (optional, can rely on conventions but kept for clarity).
     */
    protected static ?string $tableName = 'user';
    protected static ?string $primaryKey = 'id';

    /** @var int|null Primary key */
    protected ?int $id = null;

    /** @var string Display name of the user */
    protected string $name = '';

    /** @var string User email (unique) */
    protected string $email;

    /** @var string Password hash (bcrypt or similar) */
    protected string $password_hash;

    /** @var string User role: 'user' | 'admin' */
    protected string $role = 'user';

    /**
     * IIdentity implementation – returns a human-readable username.
     * In this app we treat email as the unique identifier shown to users.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Find a user by email.
     */
    public static function findByEmail(string $email): ?self
    {
        $users = static::getAll('email = :email', ['email' => $email], limit: 1);
        return $users[0] ?? null;
    }

    /**
     * Factory for registering (creating) a new user with hashed password.
     */
    public static function register(string $email, string $plainPassword, string $role = 'user'): self
    {
        $user = new self();
        $user->setEmail($email);
        $user->setPassword($plainPassword);
        $user->setRole($role);
        $user->save();
        return $user;
    }

    /**
     * Encapsulated email setter – can be extended with validation.
     */
    public function setEmail(string $email): void
    {
        // Basic trim; place for additional validation if needed
        $this->email = trim($email);
    }

    /**
     * Encapsulated role setter – ensures only allowed roles are set.
     */
    public function setRole(string $role): void
    {
        $allowed = ['user', 'admin'];
        if (!in_array($role, $allowed, true)) {
            // Fallback to default user role if invalid value is provided
            $role = 'user';
        }
        $this->role = $role;
    }

    /**
     * Set a new password from plain text – hashes internally.
     */
    public function setPassword(string $plainPassword): void
    {
        $this->password_hash = password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    /**
     * Verify a plain-text password against stored hash.
     */
    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password_hash);
    }

    /**
     * Change user password and persist it.
     */
    public function changePassword(string $newPlainPassword): void
    {
        $this->setPassword($newPlainPassword);
        $this->save();
    }

    /**
     * Check if user has admin role.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getDisplayName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = trim($name);
    }
}