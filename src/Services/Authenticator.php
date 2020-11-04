<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\UserNotFoundException;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class Authenticator
{
    private UserRepository $userRepository;
    private UserManager $userManager;

    public function __construct(UserRepository $userRepository, UserManager $userManager)
    {
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
    }

    public function authenticateUser(string $email, string $password)
    {
        $user = $this->userRepository->findOneBy(
            [
                'email' => $email,
                'password' => $this->userManager->hashPassword($password),
            ]
        );

        if ($user !== null) {
            $session = new Session(new NativeSessionStorage(), new AttributeBag());
            $session->set('user_id', $user->getId());
        } else {
            throw new UserNotFoundException('No User with provided email/pass combination found.');
        }
    }
}
