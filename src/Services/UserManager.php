<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\User;
use App\Exceptions\UserAlreadyExistsException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function createUser(User $user, string $password): User
    {
        if ($this->userDoesNotExist($user)) {
            $this->entityManager->persist($user);
            $user->setPassword(password_hash($password, PASSWORD_ARGON2ID));
        } else {
            throw new UserAlreadyExistsException('User already exists');
        }
    }

    private function userDoesNotExist(User $user): bool
    {
        return
            !(
                $this->userRepository->usernameExists($user->getUsername())
                || $this->userRepository->emailExists($user->getEmail())
            )
        ;
    }
}
