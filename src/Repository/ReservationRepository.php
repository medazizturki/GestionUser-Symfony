<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function save(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Reservation[] Returns an array of Reservation objects
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

//    public function findOneBySomeField($value): ?Reservation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


public function SortByidReservation(){
    return $this->createQueryBuilder('e')
        ->orderBy('e.idReservation','ASC')
        ->getQuery()
        ->getResult()
        ;
}

public function SortBydateDebut()
{
    return $this->createQueryBuilder('e')
        ->orderBy('e.dateDebut','ASC')
        ->getQuery()
        ->getResult()
        ;
}


public function SortBydescriptionReservation()
{
    return $this->createQueryBuilder('e')
        ->orderBy('e.descriptionReservation','ASC')
        ->getQuery()
        ->getResult()
        ;
}

public function findByidReservation( $idReservation)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.idReservation LIKE :idReservation')
        ->setParameter('idReservation','%' .$idReservation. '%')
        ->getQuery()
        ->execute();
}
public function findBydateDebut( $dateDebut)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.dateDebut LIKE :dateDebut')
        ->setParameter('dateDebut','%' .$dateDebut. '%')
        ->getQuery()
        ->execute();
}
public function findBydescriptionReservation( $descriptionReservation)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.descriptionReservation LIKE :descriptionReservation')
        ->setParameter('descriptionReservation','%' .$descriptionReservation. '%')
        ->getQuery()
        ->execute();
}

}
