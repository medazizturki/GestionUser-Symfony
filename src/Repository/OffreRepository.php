<?php

namespace App\Repository;

use App\Entity\Offre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offre>
 *
 * @method Offre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Offre[]    findAll()
 * @method Offre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OffreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offre::class);
    }

    public function save(Offre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Offre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Offre[] Returns an array of Offre objects
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

//    public function findOneBySomeField($value): ?Offre
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


public function SortByNomoffre(){
    return $this->createQueryBuilder('e')
        ->orderBy('e.nomOffre','ASC')
        ->getQuery()
        ->getResult()
        ;
}
public function SortByDate(){
    return $this->createQueryBuilder('e')
        ->orderBy('e.datepubOffre','ASC')
        ->getQuery()
        ->getResult()
        ;
}








public function findByDate( $datepubOffre)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.datepubOffre LIKE :datepubOffre')
        ->setParameter('datepubOffre','%' .$datepubOffre. '%')
        ->getQuery()
        ->execute();
}
public function findBynomoffre( $nomOffre)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.nomOffre LIKE :nomOffre')
        ->setParameter('nomOffre','%' .$nomOffre. '%')
        ->getQuery()
        ->execute();
}

}
