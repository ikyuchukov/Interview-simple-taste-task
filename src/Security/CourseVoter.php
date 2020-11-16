<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Course;
use App\Entity\User;
use App\Services\CourseManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class CourseVoter extends Voter
{
    const ROLE_ADMIN = User::ROLE_ADMIN;
    const ROLE_VIEWER = User::ROLE_VIEWER;
    const COURSE_VIEWING = 'course_viewing';

    private Security $security;
    private CourseManager $courseManager;

    public function __construct(Security $security, CourseManager $courseManager)
    {
        $this->security = $security;
        $this->courseManager = $courseManager;
    }

    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, [self::COURSE_VIEWING])) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        if ($this->security->isGranted(self::ROLE_ADMIN)) {
            return true;
        }

        if ($this->security->isGranted(self::ROLE_VIEWER) && $attribute === self::COURSE_VIEWING) {

           return $this->hasVisitsLeft($token);
        }

        return false;
    }

    private function hasVisitsLeft(TokenInterface $token): bool
    {
        return $this->courseManager->hasVisitsLeft($token->getUser());
    }

}
