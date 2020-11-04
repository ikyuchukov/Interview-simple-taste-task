<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\UserLogin;
use App\Form\UserRegister;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private CourseRepository $coursesRepository;

    public function __construct(CourseRepository $coursesRepository)
    {
        $this->coursesRepository = $coursesRepository;
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(): Response
    {
        $form = $this->createForm(UserLogin::class);

        return $this->render('authentication/login.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(): Response
    {
        $form = $this->createForm(UserRegister::class);

        return $this->render('authentication/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/home", name="home")
     */
    public function home(): Response
    {
        $courses = $this->coursesRepository->findAll();

        return $this->render('courses/home.html.twig', ['courses' => $courses]);
    }
}
