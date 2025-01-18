<?php

declare(strict_types = 1);

namespace App\Contracts;

use App\DataObjects\RegisterUserData;

interface AuthInterface
{
    public function user(): ?UserInterface;
    public function attempt(array $credentials): bool;
    public function checkCredentials(UserInterface $user, array $credentials): bool;
    public function logout(): void;
    public function login(UserInterface $user): void;
    public function register(RegisterUserData $data): UserInterface;
}
