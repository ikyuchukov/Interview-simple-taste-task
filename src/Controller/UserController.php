<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Exceptions\UserAlreadyExistsException;
use App\Services\UserDataValidation;
use App\Services\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private UserManager $userManager;
    private UserDataValidation $userDataValidation;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UserManager $userManager,
        UserDataValidation $userDataValidation,
        EntityManagerInterface $entityManager
    ) {
        $this->userManager = $userManager;
        $this->userDataValidation = $userDataValidation;
        $this->entityManager = $entityManager;
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
                $this->entityManager->flush();
                $this->addFlash('Message', 'Registration successfull');

                return $this->redirectToRoute('login');
            } catch (UserAlreadyExistsException $userAlreadyExistsException) {
                $this->addFlash('Error', 'User already exists');

                return $this->redirectToRoute('register');
            }
        } else {
            $this->addFlash('Error', 'Please fill all fields');

            return $this->redirectToRoute('register');
        }
    }
}
