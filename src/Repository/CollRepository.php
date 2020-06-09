<?php

namespace App\Repository;

use App\Entity\Coll;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Coll|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coll|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coll[]    findAll()
 * @method Coll[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coll::class);
    }

    // /**
    //  * @return Coll[] Returns an array of Coll objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Coll
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
