<?php
/**
 * Created by PhpStorm.
 * User: jaroslawjarczewski
 * Date: 23/10/2018
 * Time: 22:47
 */

namespace App\DataFixtures;


use App\Entity\Cart;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CartFixtures extends Fixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $cart = new Cart();
        $manager->persist($cart);

        $manager->flush();
    }
}