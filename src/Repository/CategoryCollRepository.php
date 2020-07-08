<?php

namespace App\Repository;

use App\Entity\CategoryColl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategoryColl|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryColl|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryColl[]    findAll()
 * @method CategoryColl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryCollRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryColl::class);
    }

    // /**
    //  * @return CategoryColl[] Returns an array of CategoryColl objects
    //  */

    public function findCatIfPlus1()
    {
        return $this->createQueryBuilder('cat')
            ->innerJoin('cat.Coll','cc')
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?CategoryColl
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
