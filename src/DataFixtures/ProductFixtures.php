<?php
/**
 * Created by PhpStorm.
 * User: jaroslawjarczewski
 * Date: 23/10/2018
 * Time: 16:02
 */

namespace App\DataFixtures;


use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Container\ContainerInterface;

class ProductFixtures extends Fixture
{
    private $data = [
        [
            'id' => 1,
            'title' => 'Fallout',
            'price' => 1.99
        ],
        [
            'id' => 2,
            'title' => 'Don\'t Starve',
            'price' => 2.99
        ],
        [
            'id' => 3,
            'title' => 'Baldur\'s Gate',
            'price' => 3.99
        ],
        [
            'id' => 4,
            'title' => 'Icewind Dale',
            'price' => 4.99
        ],
        [
            'id' => 5,
            'title' => 'Bloodborne',
            'price' => 5.99
        ],
    ];

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach($this->data as $product) {
            $productEntity = new Product();
            $productEntity->setId($product['id']);
            $productEntity->setTitle($product['title']);
            $productEntity->setPrice($product['price']);
            $manager->persist($productEntity);
        }

        $manager->flush();
    }
}