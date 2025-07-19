<?php

declare(strict_types=1);

namespace App\Model\Services;

use App\Model\Entities\User;
use App\Model\Enums\UserRole;
use App\Model\Managers\UserManager;
use Nette\Security\Passwords;

final class UserService extends BaseService
{
    private readonly Passwords $passwords;

    public function __construct(
        private readonly UserManager $userManager
    ) {
        $this->passwords = new Passwords(PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function get(int $id): ?User
    {
        return $this->userManager->find($id);
    }

    public function getByEmail(string $email): ?User
    {
        return $this->userManager->findByEmail($email);
    }

    public function getAll(): array
    {
        return $this->userManager->findAll();
    }

    public function delete(User $user): void
    {
        $this->userManager->delete($user);
    }

    public function create(array $data): ?User
    {
        if ($this->validateData($data)) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setName($data['name']);
            $user->setPasswordHash($this->passwords->hash($data['password']));
            $user->setRole(UserRole::from($data['role']));
            $user = $this->userManager->create($user);
            return $user;
        }
        $this->error = 'User insert failed, ' . $this->error;
        return null;
    }

    public function update(?string $json, ?int $id): ?User
    {
        if (!$this->isJsonValid($json)) {
            $this->error = 'Data format error';
            return null;
        }

        $user = $this->get($id);
        if (!$user) {
            $this->error = 'User not found';
            return null;
        }

        $data = json_decode($json, true);

        if (isset($data['email']) && $data['email'] && $this->validateEmail($data['email'], $user)) {
            $user->setEmail($data['email']);
        }

        if (isset($data['name']) && $data['name']) {
            $user->setName($data['name']);
        }

        if (isset($data['password']) && $data['password']) {
            $user->setPasswordHash($this->passwords->hash($data['password']));
        }

        if (isset($data['role']) && $data['role'] && UserRole::tryFrom($data['role']) !== null) {
            $user->setRole(UserRole::from($data['role']));
        }

        $user = $this->userManager->update($user);
        return $user;
    }

    private function validateData(array $data, ?User $user = null): bool
    {
        if (!isset($data['email']) || empty($data['email'])) {
            $this->error = ' E-mail is required';
            return false;
        }

        if (!$this->validateEmail($data['email'], $user)) {
            return false;
        }

        if (!isset($data['password']) || empty($data['password'])) {
            $this->error = ' Password is required';
            return false;
        }

        if (!isset($data['role'])) {
            $this->error = ' Role is required';
            return false;
        }

        if (UserRole::tryFrom($data['role']) === null) {
            $this->error = ' Role note exist';
            return false;
        }

        if (!isset($data['name']) || empty($data['name'])) {
            $this->error = ' Name is required';
            return false;
        }

        return true;
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return $this->passwords->verify($password, $hash);
    }

    private function validateEmail(string $email, ?User $editedUser): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error = ' E-mail is invalid';
            return false;
        }

        $user = $this->getByEmail($email);
        if ($user && (!$editedUser || $user !== $editedUser)) {
            $this->error = ' E-mail is duplicit';
            return false;
        }
        return true;
    }
}
