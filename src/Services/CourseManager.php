<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Course;
use App\Entity\User;
use App\Entity\UserVisit;
use App\Repository\CourseRepository;
use App\Repository\UserVisitRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class CourseManager
{
    private CourseRepository $courseRepository;
    private Authenticator $authenticator;
    private UserVisitRepository $userVisitRepository;
    private EntityManagerInterface $entityManager;
    private ContainerBagInterface $params;
    private DateTimeImmutable $currentDate;

    public function __construct(
        CourseRepository $courseRepository,
        Authenticator $authenticator,
        UserVisitRepository $userVisitRepository,
        EntityManagerInterface $entityManager,
        ContainerBagInterface $params,
        DateTimeImmutable $currentDate
    ) {
        $this->courseRepository = $courseRepository;
        $this->authenticator = $authenticator;
        $this->userVisitRepository = $userVisitRepository;
        $this->entityManager = $entityManager;
        $this->params = $params;
        $this->currentDate = $currentDate;
    }

    public function getCourseForUser(User $user, int $courseId): Course
    {
        if ($this->canUserSeeCourse($user)) {
            $this->createUserVisit($user);

            return $this->courseRepository->find($courseId);
        } else {
            return (new Course)->setName($this->courseRepository->find($courseId)->getName());
        }
    }

    public function canUserSeeCourse(User $user): bool
    {
        return $this->authenticator->isUserAdmin($user) || $this->hasVisitsLeft($user);
    }

    private function hasVisitsLeft(User $user): bool
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
