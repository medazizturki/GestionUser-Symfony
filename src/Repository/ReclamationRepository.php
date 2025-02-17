<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reclamation>
 *
 * @method Reclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reclamation[]    findAll()
 * @method Reclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    public function save(Reclamation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reclamation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Reclamation[] Returns an array of Reclamation objects
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

//    public function findOneBySomeField($value): ?Reclamation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function SortBynomReclamation(){
    return $this->createQueryBuilder('e')
        ->orderBy('e.nomReclamation','ASC')
        ->getQuery()
        ->getResult()
        ;
}

public function SortByprenomReclamation()
{
    return $this->createQueryBuilder('e')
        ->orderBy('e.prenomReclamation','ASC')
        ->getQuery()
        ->getResult()
        ;
}


public function SortBytypeReclamation()
{
    return $this->createQueryBuilder('e')
        ->orderBy('e.typeReclamation','ASC')
        ->getQuery()
        ->getResult()
        ;
}








public function findBynomReclamation( $nomReclamation)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.nomReclamation LIKE :nomReclamation')
        ->setParameter('nomReclamation','%' .$nomReclamation. '%')
        ->getQuery()
        ->execute();
}
public function findByprenomReclamation( $prenomReclamation)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.prenomReclamation LIKE :prenomReclamation')
        ->setParameter('prenomReclamation','%' .$prenomReclamation. '%')
        ->getQuery()
        ->execute();
}
public function findBytypeReclamation( $typeReclamation)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.typeReclamation LIKE :typeReclamation')
        ->setParameter('typeReclamation','%' .$typeReclamation. '%')
        ->getQuery()
        ->execute();
}

}
