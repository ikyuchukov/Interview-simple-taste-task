<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Course;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\Alice\Loader\NativeLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCourseFixturesCommand extends Command
{
    const NUMBER_OF_FIXTURES_TO_CREATE = 100;

    protected static $defaultName = 'app:create-course-fixtures';

    private EntityManagerInterface $entityManager;
    private NativeLoader $fixturesLoader;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->fixturesLoader = new NativeLoader();
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Generates Course fixtures and inserts them to the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $courseFixtures = $this->fixturesLoader->loadFile('src/Fixtures/Resources/course.yml');

        foreach ($courseFixtures->getObjects() as $course) {
            $this->entityManager->persist($course);
        }
        $this->entityManager->flush();
        $output->writeln(sprintf('Inserted %s courses', count($courseFixtures->getObjects())));

        return Command::SUCCESS;
    }
}
