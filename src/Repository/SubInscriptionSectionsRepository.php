<?php

namespace App\Repository;

use App\Entity\SubInscriptionSections;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SubInscriptionSections|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubInscriptionSections|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubInscriptionSections[]    findAll()
 * @method SubInscriptionSections[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubInscriptionSectionsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SubInscriptionSections::class);
    }

    // /**
    //  * @return SubInscriptionSections[] Returns an array of SubInscriptionSections objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SubInscriptionSections
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
