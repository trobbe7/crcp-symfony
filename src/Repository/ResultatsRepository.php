<?php

namespace App\Repository;

use App\Entity\Resultats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Resultats>
 *
 * @method Resultats|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resultats|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resultats[]    findAll()
 * @method Resultats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resultats::class);
    }

    public function save(Resultats $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Resultats $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllByID($uid): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.uid = :uid')
            ->setParameter('uid', $uid)
            ->orderBy('r.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function retrieveGraphActualMonth($uid): array
    {
        return $this->createQueryBuilder('r')
        ->where("r.created_at >= :firstOfMonth")
        ->andWhere("r.created_at <= :lastOfMonth")
        ->andWhere('r.uid = :uid')
        ->setParameter('firstOfMonth', (new \DateTimeImmutable())->modify('first day of this month')->setTime(0, 0, 0))
        ->setParameter('lastOfMonth', (new \DateTimeImmutable())->modify('last day of this month')->setTime(23, 59, 59))
            ->setParameter('uid', $uid)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getActualMonthWorkingTime($uid): float
    {
        return $this->createQueryBuilder('r')
            ->select('COALESCE(SUM(r.full_time), 0) AS ActualMonthWorkingTime')
            ->where("r.created_at >= :firstOfMonth")
            ->andWhere("r.created_at <= :lastOfMonth")
            ->andWhere('r.uid = :uid')
            ->setParameter('firstOfMonth', (new \DateTimeImmutable())->modify('first day of this month')->setTime(0, 0, 0))
            ->setParameter('lastOfMonth', (new \DateTimeImmutable())->modify('last day of this month')->setTime(23, 59, 59))
            ->setParameter('uid', $uid)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getActualMonthWorkingMinutes($uid): float
    {
        return $this->createQueryBuilder('r')
            ->select('COALESCE(SUM(r.time_minutes), 0) AS ActualMonthWorkingMinutes')
            ->where("r.created_at >= :firstOfMonth")
            ->andWhere("r.created_at <= :lastOfMonth")
            ->andWhere('r.uid = :uid')
            ->setParameter('firstOfMonth', (new \DateTimeImmutable())->modify('first day of this month')->setTime(0, 0, 0))
            ->setParameter('lastOfMonth', (new \DateTimeImmutable())->modify('last day of this month')->setTime(23, 59, 59))
            ->setParameter('uid', $uid)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getActualMonthTraitements($uid): int
    {
        return $this->createQueryBuilder('r')
            ->select('COALESCE(SUM(r.traitements), 0) AS ActualMonthTraitements')
            ->where("r.created_at >= :firstOfMonth")
            ->andWhere("r.created_at <= :lastOfMonth")
            ->andWhere('r.uid = :uid')
            ->setParameter('firstOfMonth', (new \DateTimeImmutable())->modify('first day of this month')->setTime(0, 0, 0))
            ->setParameter('lastOfMonth', (new \DateTimeImmutable())->modify('last day of this month')->setTime(23, 59, 59))
            ->setParameter('uid', $uid)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countTodayTraitements($uid): int
    {
        return $this->createQueryBuilder('r')
            ->select('count(r) as TodayTraitements')
            ->where("r.created_at >= :today")
            ->andWhere("r.created_at < :tomorrow")
            ->andWhere('r.uid = :uid')
            ->setParameter('today', (new \DateTimeImmutable())->setTime(0, 0, 0))
            ->setParameter('tomorrow', (new \DateTimeImmutable())->setTime(0, 0, 0)->modify('+1 day'))
            ->setParameter('uid', $uid)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getLastTraitement($uid): ?Resultats
    {
        return $this->createQueryBuilder('r')
            ->where('r.uid = :uid')
            ->setParameter('uid', $uid)
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getFromYM($year, $month, $uid): array
    {
        $from_date = $year . "-" . $month . "-01 00:00:00"; // User Input
        $to_date = date('Y-m-t 23:59:59', strtotime($from_date)); // t dans le timestamp correspond au dernier jour du mois défini

        return $this->createQueryBuilder('r')
        ->where("r.created_at BETWEEN :from_date AND :to_date")
        ->andWhere('r.uid = :uid')
        ->setParameter('from_date', $from_date)
        ->setParameter('to_date', $to_date)
        ->setParameter('uid', $uid)
        ->getQuery()
        ->getResult();
    }


    //    /**
    //     * @return Resultats[] Returns an array of Resultats objects
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

    //    public function findOneBySomeField($value): ?Resultats
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
