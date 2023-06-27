<?php

namespace App\Repository;

use App\Entity\ReglementBPayLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReglementBPayLink>
 *
 * @method ReglementBPayLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReglementBPayLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReglementBPayLink[]    findAll()
 * @method ReglementBPayLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReglementBPayLinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReglementBPayLink::class);
    }

    public function add(ReglementBPayLink $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ReglementBPayLink $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ReglementBPayLink[] Returns an array of ReglementBPayLink objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ReglementBPayLink
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}