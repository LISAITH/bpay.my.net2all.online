<?php

namespace App\Repository;

use App\Entity\BPayProviderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BPayProviderItem>
 *
 * @method BPayProviderItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method BPayProviderItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method BPayProviderItem[]    findAll()
 * @method BPayProviderItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BPayPaymentProviderItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BPayProviderItem::class);
    }

    public function save(BPayProviderItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BPayProviderItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return BPayProviderItem[] Returns an array of BPayProviderItem objects
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

//    public function findOneBySomeField($value): ?BPayProviderItem
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
