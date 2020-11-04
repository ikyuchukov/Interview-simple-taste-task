<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\User;
use App\Exceptions\UserAlreadyExistsException;
use App\Repository\UserRepository;
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

    public function createUser(string $username, string $email, string $password): User
    {
        $user = (new User())->setUsername($username)->setEmail($email);
        if ($this->userDoesNotExist($user)) {
            $this->entityManager->persist($user);
            $user->setPassword($this->hashPassword($password));

            return $user;
        } else {
            throw new UserAlreadyExistsException('User already exists');
        }
    }

    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
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
