<?php

declare(strict_types=1);

namespace App\Model\Managers;

use App\Model\Entities\User;
use Doctrine\ORM\EntityManagerInterface;

final class UserManager
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
    }

    public function find(int $id): ?User
    {
        return $this->em->getRepository(User::class)->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    public function findAll(): array
    {
        return $this->em->getRepository(User::class)->findAll();
    }

    public function create(User $user): User
    {
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    public function update(User $user): User
    {
        $this->em->flush();
        return $user;
    }

    public function delete(User $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}
