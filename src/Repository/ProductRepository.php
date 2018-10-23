<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    const LIMIT_PER_PAGE = 3;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function countOffset($page) {
        $page = (int) $page;
        if($page <= 0) {
            $page = 1;
        }

        return ($page - 1) * ProductRepository::LIMIT_PER_PAGE;
    }

    public function listPagination($page)
    {
        $offset = $this->countOffset($page);

        return $this->findBy([],['id'=>'asc'],ProductRepository::LIMIT_PER_PAGE, $offset);
    }
}
