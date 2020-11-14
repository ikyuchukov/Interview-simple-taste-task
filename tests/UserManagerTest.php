<?php

namespace App\Tests;

use App\Entity\User;
use App\Exceptions\UserAlreadyExistsException;
use App\Repository\UserRepository;
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
        $this->entityManager->expects($this->once())->method('persist')->with($user);

        $createdUser = $this->userManager->createUser($username, $email, $password);
        $this->assertNotNull($createdUser->getPassword());

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
        $this->userRepository->expects($this->any())->method('usernameExists')->willReturn($usernameExists);
        $this->userRepository->expects($this->any())->method('emailExists')->willReturn($emailExists);
        $this->expectException(UserAlreadyExistsException::class);
        $this->userManager->createUser($username, $email, $password);
    }

    public function createUserProvider(): array
    {
        return [
            'Normal User Registration' =>
                [
                    'gotvach_sf',
                    'dev_recepti@abv.bg',
                    'sudo123',
                    (new User())->setEmail('dev_recepti@abv.bg')->setUsername('gotvach_sf')
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
