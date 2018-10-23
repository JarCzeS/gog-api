<?php

namespace App\Repository;

use App\Entity\CartProducts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CartProducts|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartProducts|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartProducts[]    findAll()
 * @method CartProducts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartProductsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CartProducts::class);
    }

//    /**
//     * @return CartProducts[] Returns an array of CartProducts objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CartProducts
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
