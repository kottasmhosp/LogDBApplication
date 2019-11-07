<?php

namespace App\Repository;

use App\Entity\ExceptionLogs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ExceptionLogs|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExceptionLogs|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExceptionLogs[]    findAll()
 * @method ExceptionLogs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExceptionLogsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ExceptionLogs::class);
    }

    // /**
    //  * @return ExceptionLogs[] Returns an array of ExceptionLogs objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExceptionLogs
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
