<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Course;
use App\Entity\User;
use App\Entity\UserVisit;
use App\Exceptions\CourseNotFoundException;
use App\Repository\CourseRepository;
use App\Repository\UserVisitRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CourseManager
{
    private CourseRepository $courseRepository;
    private UserVisitRepository $userVisitRepository;
    private EntityManagerInterface $entityManager;
    private ContainerBagInterface $params;
    private DateTimeImmutable $currentDate;

    public function __construct(
        CourseRepository $courseRepository,
        UserVisitRepository $userVisitRepository,
        EntityManagerInterface $entityManager,
        ContainerBagInterface $params,
        DateTimeImmutable $currentDate
    ) {
        $this->courseRepository = $courseRepository;
        $this->userVisitRepository = $userVisitRepository;
        $this->entityManager = $entityManager;
        $this->params = $params;
        $this->currentDate = $currentDate;
    }

    /**
     * @param User $user
     * @param int $courseId
     *
     * @return Course
     * @throws CourseNotFoundException
     */
    public function getCourseForUser(User $user, int $courseId): Course
    {
        $course = $this->courseRepository->find($courseId);
        if ($course === null) {
            throw new CourseNotFoundException(sprintf('Course %s not found.', $courseId));
        }
        $this->createUserVisit($user);


        return $course;

    }

    public function hasVisitsLeft(UserInterface $user): bool
    {
        $userVisit = $this->userVisitRepository->findOneBy(['user' => $user]);

        return
            $userVisit === null
            || $userVisit->getCounter() < $this->params->get('course_visits')
            || $this->shouldResetUserVisits($userVisit)
        ;
    }

    private function shouldResetUserVisits(UserVisit $userVisit): bool
    {
        return
            $this->currentDate
            >= $userVisit->getLastVisit()->add(new DateInterval($this->params->get('course_visit_reset')))
        ;
    }

    private function createUserVisit(User $user)
    {
        $userVisit = $this->userVisitRepository->findOneBy(['user' => $user]);
        if ($userVisit !== null) {
            if ($this->shouldResetUserVisits($userVisit)) {
                $userVisit->setCounter(1);
            } else {
                $userVisit->setCounter($userVisit->getCounter() + 1);
            }
            $userVisit->setLastVisit($this->currentDate);
        } else {
            $userVisit = (new UserVisit())->setCounter(1)->setLastVisit($this->currentDate)->setUser($user);
            $this->entityManager->persist($userVisit);
        }
    }
}
