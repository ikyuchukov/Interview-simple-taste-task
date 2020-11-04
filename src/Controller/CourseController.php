<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exceptions\UserNotLoggedException;
use App\Repository\CourseRepository;
use App\Services\Authenticator;
use App\Services\CourseManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CourseController extends AbstractController
{
    private CourseRepository $coursesRepository;
    private Authenticator $authenticator;
    private CourseManager $courseManager;
    private EntityManagerInterface $entityManager;

    public function __construct(
        CourseRepository $coursesRepository,
        Authenticator $authenticator,
        CourseManager $courseManager,
        EntityManagerInterface $entityManager
    ) {
        $this->coursesRepository = $coursesRepository;
        $this->authenticator = $authenticator;
        $this->courseManager = $courseManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/course/{id}", name="course")
     * @param int $id
     *
     * @return Response
     * @throws UserNotLoggedException
     */
    public function course(int $id): Response
    {
        if ($this->authenticator->isUserAuthenticated()) {
            $course = $this->courseManager->getCourseForUser($this->authenticator->getLoggedUser(), $id);
            $this->entityManager->flush();

            return $this->render('courses/course.html.twig', ['course' => $course]);
        } else {
            return $this->redirectToRoute('home');
        }
    }
}

