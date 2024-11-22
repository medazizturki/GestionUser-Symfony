<?php

namespace App\Repository;

use App\Entity\Rendezvous;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rendezvous>
 *
 * @method Rendezvous|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rendezvous|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rendezvous[]    findAll()
 * @method Rendezvous[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RendezvousRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rendezvous::class);
    }

    public function save(Rendezvous $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Rendezvous $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Rendezvous[] Returns an array of Rendezvous objects
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

//    public function findOneBySomeField($value): ?Rendezvous
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function nbcat(){

    $qb= $this->createQueryBuilder('r')
    ->select('Count(r)');
    return $qb->getQuery()->getSingleScalarResult();

    /**
     * Solution avec DQL
     */
    /*$entityManager = $this->getEntityManager();
    $query = $entityManager
        ->createQuery('SELECT Count(c) FROM App\Entity\Category s ')
    return  $query->getResult();*/

}
public function SortByNomRendezvous(){
    return $this->createQueryBuilder('e')
        ->orderBy('e.nomRendezvous','ASC')
        ->getQuery()
        ->getResult()
        ;
}

public function SortByEmailRendezvous()
{
    return $this->createQueryBuilder('e')
        ->orderBy('e.emailRendezvous','ASC')
        ->getQuery()
        ->getResult()
        ;
}


public function SortByPrenomRendezvous()
{
    return $this->createQueryBuilder('e')
        ->orderBy('e.prenomRendezvous','ASC')
        ->getQuery()
        ->getResult()
        ;
}








public function findBynomRendezvous( $nomRendezvous)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.nomRendezvous LIKE :nomRendezvous')
        ->setParameter('nomRendezvous','%' .$nomRendezvous. '%')
        ->getQuery()
        ->execute();
}
public function findByemailRendezvous( $emailRendezvous)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.emailRendezvous LIKE :emailRendezvous')
        ->setParameter('emailRendezvous','%' .$emailRendezvous. '%')
        ->getQuery()
        ->execute();
}
public function findByprenomRendezvous( $prenomRendezvous)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.prenomRendezvous LIKE :prenomRendezvous')
        ->setParameter('prenomRendezvous','%' .$prenomRendezvous. '%')
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
