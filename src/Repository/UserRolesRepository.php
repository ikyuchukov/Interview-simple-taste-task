<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserRoles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserRoles|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRoles|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRoles[]    findAll()
 * @method UserRoles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRolesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRoles::class);
    }

    public function isAdmin(User $user)
    {
         $result = $this->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->andWhere('u.role = :role')
                ->andWhere('u.user = :user')
                ->setParameter('user', $user)
                ->setParameter('role', UserRoles::ROLE_ADMIN)
                ->getQuery()
                ->getSingleScalarResult()
            ;

         return (bool) $result;
    }
}
