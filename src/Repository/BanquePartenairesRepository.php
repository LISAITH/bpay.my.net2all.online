<?php

namespace App\Repository;

use App\Entity\BanquePartenaire;
use App\Entity\Pays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BanquePartenaire>
 *
 * @method BanquePartenaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method BanquePartenaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method BanquePartenaire[]    findAll()
 * @method BanquePartenaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BanquePartenairesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BanquePartenaire::class);
    }

    public function save(BanquePartenaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BanquePartenaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function filteredPartenaireBanque(?Pays $pays, ?bool $isActive)
    {
        $queryBuilder = $this->createQueryBuilder('b');
        $query = $queryBuilder->select('b')->innerJoin('b.pays', 'c');
        if (!empty($pays)) {
            $query->andWhere('c=:pays')->setParameter('pays', $pays);
        }
        if (!empty($isActive)) {
            $query->andWhere('c=:active')->setParameter('active', $isActive);
        }
        $query->orderBy('b.dateCreation', 'DESC');

        return $query->getQuery()->getResult();
    }

//    /**
//     * @return BanquePartenaire[] Returns an array of BanquePartenaire objects
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

//    public function findOneBySomeField($value): ?BanquePartenaire
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
