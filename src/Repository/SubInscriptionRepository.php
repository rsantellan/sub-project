<?php

namespace App\Repository;

use App\Entity\SubInscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SubInscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubInscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubInscription[]    findAll()
 * @method SubInscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubInscriptionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SubInscription::class);
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
            ->orderBy('n.id', 'DESC');
        return $this->createPaginator($qb->getQuery(), $page, $maxPerPage);
    }

    // /**
    //  * @return SubInscription[] Returns an array of SubInscription objects
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
    public function findOneBySomeField($value): ?SubInscription
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
