<?php

namespace App\Repository;

use App\Entity\Conge;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conge>
 *
 * @method Conge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conge[]    findAll()
 * @method Conge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CongeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conge::class);
    }

//    /**
//     * @return Conge[] Returns an array of Conge objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Conge
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * Trouve tous les congés actifs (en cours) qui sont approuvés
     * 
     * @return Conge[] Les congés actifs
     */
    public function findActiveConges(): array
    {
        $today = new \DateTime();
        
        return $this->createQueryBuilder('c')
            ->where('c.date_debut <= :today')
            ->andWhere('c.date_fin >= :today')
            ->andWhere('c.statut = :statut')
            ->setParameter('today', $today)
            ->setParameter('statut', 'accepté')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Trouve les congés actifs pour un utilisateur spécifique
     * 
     * @param User $user L'utilisateur dont on recherche les congés
     * @return Conge[] Les congés actifs de l'utilisateur
     */
    public function findActiveCongesForUser(User $user): array
    {
        $today = new \DateTime();
        
        return $this->createQueryBuilder('c')
            ->where('c.user = :user')
            ->andWhere('c.date_debut <= :today')
            ->andWhere('c.date_fin >= :today')
            ->andWhere('c.statut = :statut')
            ->setParameter('user', $user)
            ->setParameter('today', $today)
            ->setParameter('statut', 'approuvé')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre d'utilisateurs actuellement en congé
     */
    public function countActiveConges(): int
    {
        $today = new \DateTime();
        
        return $this->createQueryBuilder('c')
            ->select('COUNT(DISTINCT c.user)') 
            ->where('c.date_debut <= :today')
            ->andWhere('c.date_fin >= :today')
            ->andWhere('c.statut = :statut')
            ->setParameter('today', $today)
            ->setParameter('statut', 'approuvé') // Valeur correcte correspondant au CongeController
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compte le nombre de congés en attente de validation
     */
    public function countPendingConges(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)') 
            ->where('c.statut = :statut')
            ->setParameter('statut', 'en attente')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
