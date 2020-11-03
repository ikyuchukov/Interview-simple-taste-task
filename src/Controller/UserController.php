<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Exceptions\UserAlreadyExistsException;
use App\Services\UserDataValidation;
use App\Services\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private UserManager $userManager;
    private UserDataValidation $userDataValidation;

    public function __construct(UserManager $userManager, UserDataValidation $userDataValidation)
    {
        $this->userManager = $userManager;
        $this->userDataValidation = $userDataValidation;
    }

    /**
     * @Route("/createUser", name="createUser")
     * @param Request $request
     *
     * @return Response
     */
    public function createUser(Request $request): Response
    {
        $userData = $request->request->get('user_register');
        if ($this->userDataValidation->isUserRegistrationDataValid($userData)) {
            $user = (new User())->setUsername($userData['username'])->setEmail($userData['email']);
            try {
                $this->userManager->createUser($user, $userData['password']);
            } catch (UserAlreadyExistsException $userAlreadyExistsException) {
                $this->redirect('/register', 400);
            }
        } else {
            $this->redirect('/register', 400);
        }
    }
}
