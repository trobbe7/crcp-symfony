<?php

namespace App\Repository;

use App\Entity\Objectifs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Objectifs>
 *
 * @method Objectifs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Objectifs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Objectifs[]    findAll()
 * @method Objectifs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObjectifsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Objectifs::class);
    }

    public function save(Objectifs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Objectifs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countMonthObjectif($uid): int
    {
        return $this->createQueryBuilder('o')
            ->select('count(o) as MonthObjectif')
            ->where("o.created_at >= :firstOfMonth")
            ->andWhere("o.created_at <= :lastOfMonth")
            ->andWhere('o.uid = :uid')
            ->setParameter('firstOfMonth', (new \DateTimeImmutable())->modify('first day of this month'))
            ->setParameter('lastOfMonth', (new \DateTimeImmutable())->modify('last day of this month'))
            ->setParameter('uid', $uid)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getLastObjectif($uid): ?Objectifs
    {
        return $this->createQueryBuilder('o')
            ->where('o.uid = :uid')
            ->setParameter('uid', $uid)
            ->orderBy('o.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getActualMonthObjectif($uid): ?Objectifs
    {

        $from_date = date('y') . "-" . date('m') . "-01 00:00:00";
        $to_date = date('y') . "-" . date('m') . "-31 23:59:59";

        return $this->createQueryBuilder('o')
            ->where('o.created_at BETWEEN :from_date AND :to_date')
            ->andWhere('o.uid = :uid')
            ->setParameter('from_date', $from_date)
            ->setParameter('to_date', $to_date)
            ->setParameter('uid', $uid)
            ->orderBy('o.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Objectifs[] Returns an array of Objectifs objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Objectifs
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
