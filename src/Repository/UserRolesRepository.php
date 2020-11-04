<?php

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

    // /**
    //  * @return UserRoles[] Returns an array of UserRoles objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserRoles
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

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
