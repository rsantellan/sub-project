<?php

namespace App\Repository;

use App\Entity\SubAuthority;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SubAuthority|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubAuthority|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubAuthority[]    findAll()
 * @method SubAuthority[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubAuthorityRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SubAuthority::class);
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

    /**
     * @param $type
     * @return mixed
     */
    public function getByType($type)
    {
        return $this->createQueryBuilder('s')
                    ->andWhere('s.enable = :enable')
                    ->andWhere('s.type = :type')
                    ->setParameters(['enable' => true, 'type' => $type])
                    ->orderBy('s.position')
                    ->getQuery()
                    ->getResult()
            ;
    }

    // /**
    //  * @return SubAuthority[] Returns an array of SubAuthority objects
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
    public function findOneBySomeField($value): ?SubAuthority
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
