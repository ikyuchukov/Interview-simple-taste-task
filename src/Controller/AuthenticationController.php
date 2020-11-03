<?php

namespace App\Controller;

use App\Form\UserRegister;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticationController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(): Response
    {
        $form = $this->createForm(UserRegister::class);

        return $this->render('authentication/register.html.twig', ['form' => $form->createView()]);
    }
}
