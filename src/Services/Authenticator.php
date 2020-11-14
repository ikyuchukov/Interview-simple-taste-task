<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\User;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserNotLoggedException;
use App\Repository\UserRepository;
use App\Repository\UserRolesRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Authenticator
{
    const SESSION_USER_ID = 'user_id';

    private UserRepository $userRepository;
    private UserManager $userManager;
    private SessionInterface $session;
    private UserRolesRepository $userRolesRepository;

    public function __construct(
        UserRepository $userRepository,
        UserManager $userManager,
        SessionInterface $session,
        UserRolesRepository $userRolesRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
        $this->session = $session;
        $this->userRolesRepository = $userRolesRepository;
    }

    public function authenticateUser(string $email, string $password)
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if ($user !== null && password_verify($password, $user->getPassword())) {
            $this->session->set('user_id', $user->getId());
        } else {
            throw new UserNotFoundException('No User with provided email/pass combination found.');
        }
    }

    public function getLoggedUser(): User
    {
        $userId = $this->session->get(self::SESSION_USER_ID);
        if ($userId === null) {
            throw new UserNotLoggedException('User isn\'t currently logged.');
        }

        return $this->userRepository->find($userId);
    }

    public function isUserAuthenticated(): bool
    {
        return $this->session->get(self::SESSION_USER_ID) !== null;
    }

    public function isUserAdmin(User $user): bool
    {
        return $this->userRolesRepository->isAdmin($user);
    }
}
