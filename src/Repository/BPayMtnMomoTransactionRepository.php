<?php

namespace App\Repository;

use App\Entity\BPayMomoTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BPayMomoTransaction>
 *
 * @method BPayMomoTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method BPayMomoTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method BPayMomoTransaction[]    findAll()
 * @method BPayMomoTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BPayMtnMomoTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BPayMomoTransaction::class);
    }

    public function save(BPayMomoTransaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BPayMomoTransaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return BPayMomoTransaction[] Returns an array of BPayMomoTransaction objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BPayMomoTransaction
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
