<?php

namespace App\Tests;

use App\Entity\Course;
use App\Entity\User;
use App\Entity\UserVisit;
use App\Repository\CourseRepository;
use App\Repository\UserVisitRepository;
use App\Services\Authenticator;
use App\Services\CourseManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBag;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class CourseManagerTest extends TestCase
{
    private CourseManager $courseManager;
    /**
     * @var MockObject|CourseRepository
     */
    private $courseRepository;
    /**
     * @var MockObject|Authenticator
     */
    private $authenticator;
    /**
     * @var MockObject|UserVisitRepository
     */
    private $userVisitRepository;
    /**
     * @var MockObject|EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var MockObject|ContainerBagInterface
     */
    private $params;
    private DateTimeImmutable $currentDate;

    public function setUp()
    {
        $this->courseRepository = $this->createMock(CourseRepository::class);
        $this->authenticator = $this->createMock(Authenticator::class);
        $this->userVisitRepository = $this->createMock(UserVisitRepository::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->params = $this->createMock(ContainerBag::class);
        $this->params
            ->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnOnConsecutiveCalls(...[10, 'P1D', 'P1D'])
        ;
        $this->currentDate = new DateTimeImmutable('2020-10-10');
        $this->courseManager = new CourseManager(
            $this->courseRepository,
            $this->authenticator,
            $this->userVisitRepository,
            $this->entityManager,
            $this->params,
            $this->currentDate
        );
    }

    /**
     * @param User $user
     * @param Course $course
     * @param Course $expectedCourse
     * @param UserVisit $userVisit
     * @param int $visitCounter
     * @param bool $isAdmin
     *
     * @dataProvider getCourseForUserProvider
     */
    public function testGetCourseForUser(
        User $user,
        Course $course,
        Course $expectedCourse,
        UserVisit $userVisit,
        int $visitCounter,
        bool $isAdmin
    ) {
        $this->authenticator->expects($this->once())->method('isUserAdmin')->willReturn($isAdmin);
        $this->courseRepository->expects($this->once())->method('find')->willReturn($course);
        $this->userVisitRepository->expects($this->any())->method('findOneBy')->willReturn($userVisit);

        $gottenCourse = $this->courseManager->getCourseForUser($user, 1);
        $this->assertEquals($userVisit->getCounter(), $visitCounter);
        $this->assertEquals($gottenCourse, $expectedCourse);

    }

    public function getCourseForUserProvider(): array
    {
        return [
            'User has visits left' =>
                [
                    (new User),
                    (new Course)->setName('Tarator')->setUrl('youtube123'),
                    (new Course)->setName('Tarator')->setUrl('youtube123'),
                    (new UserVisit())->setCounter(5)->setLastVisit(new DateTimeImmutable('2020-10-10')),
                    6,
                    false,
                ],
            'User has no visits left' =>
                [
                    (new User),
                    (new Course)->setName('Tarator')->setUrl('youtube123'),
                    (new Course)->setName('Tarator')->setUrl(null),
                    (new UserVisit())->setCounter(10)->setLastVisit(new DateTimeImmutable('2020-10-10')),
                    10,
                    false,
                ],
            'User not visited in days' =>
                [
                    (new User),
                    (new Course)->setName('Tarator')->setUrl('youtube123'),
                    (new Course)->setName('Tarator')->setUrl('youtube123'),
                    (new UserVisit())->setCounter(10)->setLastVisit(new DateTimeImmutable('2020-05-05')),
                    1,
                    false,
                ],
        ];
    }
}
