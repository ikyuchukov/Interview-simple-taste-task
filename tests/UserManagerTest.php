<?php

namespace App\Tests;

use App\Entity\Course;
use App\Entity\User;
use App\Entity\UserVisit;
use App\Exceptions\UserAlreadyExistsException;
use App\Repository\UserRepository;
use App\Repository\UserVisitRepository;
use App\Services\UserManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserManagerTest extends TestCase
{
    private UserManager $userManager;
    /**
     * @var MockObject|UserRepository
     */
    private $userRepository;
    /**
     * @var MockObject|EntityManagerInterface
     */
    private $entityManager;

    public function setUp()
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->userManager = new UserManager($this->userRepository, $this->entityManager);
    }

    /**
     * @param string $username
     * @param string $email
     * @param string $password
     * @param User $user
     *
     * @dataProvider createUserProvider
     */
    public function testCreateUser(
        string $username,
        string $email,
        string $password,
        User $user
    ) {
        $this->userRepository->expects($this->any())->method('usernameExists')->willReturn(false);
        $this->userRepository->expects($this->any())->method('emailExists')->willReturn(false);

        $createdUser = $this->userManager->createUser($username, $email, $password);
        $this->assertEquals($userVisit->getCounter(), $visitCounter);
        $this->assertEquals($gottenCourse, $expectedCourse);
    }

    /**
     * @param string $username
     * @param string $email
     * @param string $password
     * @param bool $usernameExists
     * @param bool $emailExists
     * @dataProvider createUserAlreadyExistsProvider
     *
     * @throws UserAlreadyExistsException
     */
    public function testCreateUserAlreadyExists(
        string $username,
        string $email,
        string $password,
        bool $usernameExists,
        bool $emailExists
    ) {
        $this->userRepository->expects($this->any())->method('usernameExists')->willReturn(true);
        $this->userRepository->expects($this->any())->method('emailExists')->willReturn(true);
        $this->expectException(UserAlreadyExistsException::class);
        $this->userManager->createUser($username, $email, $password);
    }

    public function createUserProvider(): array
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

    public function createUserAlreadyExistsProvider(): array
    {
        return [
            'email exists' => [
                'Uti_Buchvarov',
                'uti@abv.bg',
                'burzolesnovkusno123',
                false,
                true,
            ],
            'username exists' => [
                'Iv@nZvezdev',
                'ivan_zv123@mail.bg',
                'podoburotUti',
                true,
                false,
            ],
        ];
    }
}
