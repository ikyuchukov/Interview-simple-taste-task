<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Course;
use App\Exceptions\CourseNotFoundException;
use App\Repository\CourseRepository;
use App\Security\CourseVoter;
use App\Services\CourseManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CourseController extends AbstractController
{
    private CourseRepository $coursesRepository;
    private AuthorizationCheckerInterface $authorizationChecker;
    private CourseManager $courseManager;
    private EntityManagerInterface $entityManager;

    public function __construct(
        CourseRepository $coursesRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        CourseManager $courseManager,
        EntityManagerInterface $entityManager
    ) {
        $this->coursesRepository = $coursesRepository;
        $this->authorizationChecker = $authorizationChecker;
        $this->courseManager = $courseManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/course/{id}", name="course")
     * @param int $id
     *
     * @return Response
     */
    public function course(int $id): Response
    {
        if($this->isGranted(CourseVoter::COURSE_VIEWING)) {
            try {
                $course = $this->courseManager->getCourseForUser($this->getUser(), $id);
                $this->entityManager->flush();

                return $this->render('courses/course.html.twig', ['course' => $course]);
            } catch (CourseNotFoundException $courseNotFoundException) {
                return
                    $this->render(
                        'courses/not_found.html.twig',
                        ['errorMessage' => $courseNotFoundException->getMessage()]
                );
            }
        } else {
            $course = $this->coursesRepository->find($id);
            if ($course !== null) {
                return $this->render('courses/course.html.twig', [
                    'course' => (new Course)->setName($course->getName())
                ]);
            }

            return
                $this->render(
                    'courses/not_found.html.twig',
                    ['errorMessage' => 'Course not found.']
                );
        }
    }
}

