<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserVisitRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserVisitRepository::class)
 */
class UserVisit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="integer")
     */
    private int $counter;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $last_visit;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function setCounter(int $counter): self
    {
        $this->counter = $counter;

        return $this;
    }

    public function getLastVisit(): DateTimeImmutable
    {
        return $this->last_visit;
    }

    public function setLastVisit(DateTimeImmutable $last_visit): self
    {
        $this->last_visit = $last_visit;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
