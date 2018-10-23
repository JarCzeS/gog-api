<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends FOSRestController
{
    /**
     * @var ProductRepository
     */
    private $repository;
    private $em;

    public function __construct(ProductRepository $repository, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Rest\Get("/products/{page}")
     * @param $page
     * @return Product[]
     */
    public function list($page = 1)
    {
        return $this->repository->listPagination($page);
    }

    /**
     * @Rest\Post("/product/add")
     * @param Request $request
     * @return View
     */
    public function add(Request $request)
    {
        $title = (string) $request->get('title');
        $price = (float) $request->get('price');

        if(empty($title) || $price <= 0)
        {
            return new View("Invalid values.", Response::HTTP_BAD_REQUEST);
        }

        $product = new Product();
        $product->setTitle($title);
        $product->setPrice($price);

        try {
            $this->em->persist($product);
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            return new View("Title already exists.", Response::HTTP_BAD_REQUEST);
        }


        return new View("Product added.", Response::HTTP_CREATED);
    }

    /**
     * @Rest\Delete("/product/delete/{id}")
     * @param $id
     * @return View
     */
    public function delete($id) {
        $product = $this->repository->find((int) $id);

        if (!$product) {
            return new View("No product found.", Response::HTTP_BAD_REQUEST);
        }

        $this->em->remove($product);
        $this->em->flush();

        return new View("Product deleted.", Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/product/update/{id}")
     * @param $id
     * @param Request $request
     * @return View
     */
    public function update($id, Request $request) {
        $title = (string) $request->get('title');
        $price = (float) $request->get('price',0);

        $product = $this->repository->find((int) $id);

        if(!empty($title)) {
            $product->setTitle($title);
        }
        if($price > 0) {
            $product->setPrice($price);
        }

        if(empty($title) && $price <= 0) {
            return new View("Invalid params.", Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->persist($product);
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            return new View("Title already exists.", Response::HTTP_BAD_REQUEST);
        }

        return new View("Product changed.", Response::HTTP_OK);
    }
}
