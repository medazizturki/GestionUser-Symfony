<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenement>
 *
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

    public function save(Evenement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Evenement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Evenement[] Returns an array of Evenement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Evenement
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }



public function SortByNomEvenement(){
    return $this->createQueryBuilder('e')
        ->orderBy('e.nomEvenement','ASC')
        ->getQuery()
        ->getResult()
        ;
}

public function SortByTypeEvenement()
{
    return $this->createQueryBuilder('e')
        ->orderBy('e.typeEvenement','ASC')
        ->getQuery()
        ->getResult()
        ;
}


public function SortBylieuEvenement()
{
    return $this->createQueryBuilder('e')
        ->orderBy('e.lieuEvenement','ASC')
        ->getQuery()
        ->getResult()
        ;
}








public function findBynomEvenement( $nomEvenement)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.nomEvenement LIKE :nomEvenement')
        ->setParameter('nomEvenement','%' .$nomEvenement. '%')
        ->getQuery()
        ->execute();
}
public function findBylieuEvenement( $lieuEvenement)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.lieuEvenement LIKE :lieuEvenement')
        ->setParameter('lieuEvenement','%' .$lieuEvenement. '%')
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
public function findBydateFin( $dateFin)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.dateFin LIKE :dateFin')
        ->setParameter('dateFin','%' .$dateFin. '%')
        ->getQuery()
        ->execute();
}

}
