<?php

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository
{
    const MAX_PRODUCT_COUNT = 10;
    const MAX_PRODUCTS_IN_CART = 3;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function find($id, $lockMode = null, $lockVersion = null) {
        /** @var Cart $cart */
        $cart = parent::find($id, $lockMode, $lockVersion);

        if($cart) {
            $cart->setTotalSum($this->countTotalPrice($cart));
        }

        return $cart;
    }

    private function countTotalPrice(Cart $cart): float {
        $sum  = 0;
        foreach($cart->getCartProducts() as $cartProduct) {
            $sum += $cartProduct->getQuantity() * $cartProduct->getPrice();
        }

        return round($sum, 2);
    }
}
