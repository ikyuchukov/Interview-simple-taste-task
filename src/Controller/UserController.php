<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Exceptions\UserAlreadyExistsException;
use App\Exceptions\UserNotFoundException;
use App\Form\UserLogin;
use App\Form\UserRegister;
use App\Services\Authenticator;
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
    private Authenticator $authenticator;

    public function __construct(
        UserManager $userManager,
        UserDataValidation $userDataValidation,
        EntityManagerInterface $entityManager,
        Authenticator $authenticator
    ) {
        $this->userManager = $userManager;
        $this->userDataValidation = $userDataValidation;
        $this->entityManager = $entityManager;
        $this->authenticator = $authenticator;
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
        $userRegister = new UserRegister();
        $form = $this->createForm(UserRegister::class, $userRegister);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->userManager->createUser($userData['username'], $userData['email'], $userData['password']);
                    $this->entityManager->flush();
                    $this->addFlash('Message', 'Registration successful');

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

    /**
     * @Route("/loginUser", name="loginUser")
     * @param Request $request
     *
     * @return Response
     */
    public function loginUser(Request $request): Response
    {
        $userData = $request->request->get('user_login');
        if ($this->userDataValidation->isUserLoginDataValid($userData)) {
            try {
                $this->authenticator->authenticateUser($userData['email'], $userData['password']);

                return $this->redirectToRoute('home');
            } catch (UserNotFoundException $userNotFoundException) {
                $this->addFlash('Error', 'User not found');

                return $this->redirectToRoute('login');
            }

        } else {
            $this->addFlash('Error', 'Please fill all fields');

            return $this->redirectToRoute('login');
        }
    }
}
