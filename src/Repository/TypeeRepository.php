<?php

namespace App\Repository;

use App\Entity\Typee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Typee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Typee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Typee[]    findAll()
 * @method Typee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Typee::class);
    }

    // /**
    //  * @return Typee[] Returns an array of Typee objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Typee
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
