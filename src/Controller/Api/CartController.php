<?php
/**
 * Created by PhpStorm.
 * User: jaroslawjarczewski
 * Date: 23/10/2018
 * Time: 20:49
 */

namespace App\Controller\Api;


use App\Entity\Cart;
use App\Entity\CartProducts;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class CartController extends FOSRestController
{
    /**
     * @var CartRepository
     */
    private $repository;
    private $em;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(CartRepository $repository, ProductRepository $productRepository, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->productRepository = $productRepository;
    }

    /**
     * @Rest\Post("/cart/create")
     * @return View
     */
    public function create()
    {
        $cart = new Cart();

        $this->em->persist($cart);
        $this->em->flush();

        return new View($cart->getId(), Response::HTTP_CREATED);
    }

    /**
     * @Rest\Post("/cart/add")
     * @param Request $request
     * @return View
     */
    public function add(Request $request)
    {
        $productId = (int) $request->get('productId',0);
        $cartId = (int) $request->get('cartId',0);
        $quantity = (int) $request->get('quantity',1);

        if(!$cartId || !$productId) {
            return new View('Invalid params.', Response::HTTP_BAD_REQUEST);
        }

        $cart = $this->repository->find($cartId);
        $product = $this->productRepository->find($productId);

        if(!$cart || !$product) {
            return new View('Invalid params.', Response::HTTP_BAD_REQUEST);
        }

        $cartProduct = $cart->getCartProducts()->filter(function(CartProducts $cartProducts) use ($product) {
            return ($cartProducts->getProduct()->getId() == $product->getId());
        })->first();

        if(!$cartProduct) {
            if(count($cart->getCartProducts()) >= CartRepository::MAX_PRODUCTS_IN_CART) {
                return new View('Cart product limit reached.', Response::HTTP_BAD_REQUEST);
            }
            $cartProduct = new CartProducts();
            $cartProduct->setPrice($product->getPrice());
            $cartProduct->setTitle($product->getTitle());
            $cartProduct->setProduct($product);
            $cartProduct->setQuantity($quantity);
            $cart->addCartProduct($cartProduct);
        }
        else {
            $quantity = $cartProduct->getQuantity() + $quantity;
            $cartProduct->setQuantity($quantity);
        }

        if($cartProduct->getQuantity() > CartRepository::MAX_PRODUCT_COUNT) {
            return new View('Max quantity of product reached.', Response::HTTP_BAD_REQUEST);
        }

        $this->em->persist($cartProduct);
        $this->em->flush();

        return new View("Product added", Response::HTTP_CREATED);
    }

    /**
     * @Rest\Delete("/cart/{cartId}/product/delete/{productId}")
     * @param int $cartId
     * @param int $productId
     * @return View
     */
    public function deleteProduct(int $cartId, int $productId)
    {
        $cart = $this->repository->find($cartId);
        $product = $this->productRepository->find($productId);

        if(!$cart || !$product) {
            return new View('Invalid params.', Response::HTTP_BAD_REQUEST);
        }

        /** @var CartProducts $cartProduct */
        $cartProduct = $cart->getCartProducts()->filter(function(CartProducts $cartProducts) use ($product) {
            return ($cartProducts->getProduct()->getId() == $product->getId());
        })->first();

        $this->em->remove($cartProduct);
        $this->em->flush();

        return new View("Product removed from cart", Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/cart/{id}")
     * @param int $id
     * @return object
     */
    public function list(int $id) {
        return $this->repository->find($id);
    }
}