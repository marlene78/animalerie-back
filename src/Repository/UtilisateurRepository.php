<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }


    // /**
    //  * @return Utilisateur[] Returns an array of Utilisateur objects
    //  */
    // public function findByExampleField($value , $role) //$value = id
    // {
    //     return $this->createQueryBuilder('u') 
    //         ->andWhere('u.id = :val') 
    //         ->setParameter('val', $value)
    //         ->andWhere('u.role = :role')
    //         ->setParameter('role', $role)
    //         ->orderBy('u.id', 'ASC')
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }
    

    /*
    public function findOneBySomeField($value): ?Utilisateur
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function findByRoled($value , $role) //$value = id
    {
    return $this->createQueryBuilder('u') 
    ->andWhere('u.id = :val') 
    ->setParameter('val', $value)
    ->andWhere('u.role = :role')
    ->setParameter('role', $role)
    ->orderBy('u.id', 'ASC')
    ->getQuery()
    ->getResult()
    ;
    }

}
