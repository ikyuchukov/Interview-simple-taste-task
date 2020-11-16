<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\Alice\Loader\NativeLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserFixturesCommand extends Command
{
    protected static $defaultName = 'app:create-user-fixtures';

    private EntityManagerInterface $entityManager;
    private NativeLoader $fixturesLoader;
    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->fixturesLoader = new NativeLoader();
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Generates User fixtures and inserts them to the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userFixtures = $this->fixturesLoader->loadFile('src/Fixtures/Resources/user.yml');

        /** @var User $user */
        foreach ($userFixtures->getObjects() as $user) {
            $output->writeln(
                sprintf(
                    'Persisting User: %s with password {%s}',
                    $user->getEmail(),
                    $user->getPassword())
            )
            ;
            $user->setPassword(
                $this->userPasswordEncoder->encodePassword(
                    $user,
                    $user->getPassword())
            )->setRoles([User::ROLE_VIEWER])
            ;
            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
