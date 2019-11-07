<?php

namespace App\Repository;

use App\Entity\HdfsLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HdfsLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method HdfsLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method HdfsLog[]    findAll()
 * @method HdfsLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HdfsLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HdfsLog::class);
    }

    // /**
    //  * @return HdfsLog[] Returns an array of HdfsLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HdfsLog
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
