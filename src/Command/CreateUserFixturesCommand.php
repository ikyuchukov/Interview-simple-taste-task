<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Services\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\Alice\Loader\NativeLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserFixturesCommand extends Command
{
    protected static $defaultName = 'app:create-user-fixtures';

    private EntityManagerInterface $entityManager;
    private NativeLoader $fixturesLoader;
    private UserManager $userManager;

    public function __construct(EntityManagerInterface $entityManager, UserManager $userManager)
    {
        $this->entityManager = $entityManager;
        $this->fixturesLoader = new NativeLoader();
        $this->userManager = $userManager;
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
            $user->setPassword($this->userManager->hashPassword($user->getPassword()));
            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
