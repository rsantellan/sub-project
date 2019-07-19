<?php

namespace App\Repository;

use App\Entity\SubSections;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SubSections|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubSections|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubSections[]    findAll()
 * @method SubSections[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubSectionsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SubSections::class);
    }

    private function createPaginator(Query $query, int $page, int $maxPerPage = 10): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage($maxPerPage);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    public function findLatest(int $page, int $maxPerPage = 10) : Pagerfanta
    {
        $qb = $this->createQueryBuilder('n')
            ->addSelect('n')
            ->orderBy('n.position', 'ASC');
        return $this->createPaginator($qb->getQuery(), $page, $maxPerPage);
    }

    public function findAllByPosition()
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return SubSections[] Returns an array of SubSections objects
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
    public function findOneBySomeField($value): ?SubSections
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
