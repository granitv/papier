<?php

namespace App\Repository;

use App\Entity\PageData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PageData|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageData|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageData[]    findAll()
 * @method PageData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageData::class);
    }

    // /**
    //  * @return PageData[] Returns an array of PageData objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PageData
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
