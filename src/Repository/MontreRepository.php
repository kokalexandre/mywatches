<?php

namespace App\Repository;

use App\Entity\Montre;
use App\Entity\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Montre>
 */
class MontreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Montre::class);
    }

    //    /**
    //     * @return Montre[] Returns an array of Montre objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Montre
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
    * @return Montre[] Retourne les montres du membre donné
    */
    public function findMemberMontres(Member $member): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.coffre', 'c')
            ->leftJoin('c.member', 'mb')
            ->andWhere('mb = :member')
            ->setParameter('member', $member)
            ->getQuery()
            ->getResult()
        ;
    }
}
