<?php

namespace App\Repository;

use App\Entity\BPayTransaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BPayTransaction>
 *
 * @method BPayTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method BPayTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method BPayTransaction[]    findAll()
 * @method BPayTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BPayPaymentTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BPayTransaction::class);
    }

    public function save(BPayTransaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BPayTransaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getTransactionHistories(User $user)
    {
        $queryBuilder = $this->createQueryBuilder('t');
        $query = $queryBuilder->select('t.transaction_date as date', ' t.amount as nb')
            ->andWhere('t.user_id=:userId')->andWhere('t.transaction_date IS NOT NULL')->setParameter('userId', (int) $user->getId());

        return $query->getQuery()->getResult();
    }

//    /**
//     * @return BPayMomoTransaction[] Returns an array of BPayMomoTransaction objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BPayMomoTransaction
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
