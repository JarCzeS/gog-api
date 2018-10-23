<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CartControllerTest extends DataFixtureTestCase
{
    public function setUp() {
        parent::setUp();
    }

    public function testAddCart() {
        list($json, $contentType) = $this->makeRequest('POST', '/cart/create');

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->assertContains('2', $json);
    }

    public function testAddProductToCart() {
        list($json, $contentType) = $this->makeRequest('POST', '/cart/add', ['productId' => 1, 'cartId' => 1]);

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Product added', $json);
    }

    public function testAddProductToInvalidCart() {
        list($json, $contentType) = $this->makeRequest('POST', '/cart/add', ['productId' => 1, 'cartId' => 10]);

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Invalid params', $json);
    }

    public function testAddInvalidProductToCart() {
        list($json, $contentType) = $this->makeRequest('POST', '/cart/add', ['productId' => 'xxx', 'cartId' => 1]);

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Invalid params', $json);
    }

    public function testAddInvalidParams() {
        list($json, $contentType) = $this->makeRequest('POST', '/cart/add');

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Invalid params', $json);
    }

    public function testCartProductLimit() {
        $this->makeRequest('POST', '/cart/add', ['productId' => 1, 'cartId' => 1]);
        $this->makeRequest('POST', '/cart/add', ['productId' => 2, 'cartId' => 1]);
        $this->makeRequest('POST', '/cart/add', ['productId' => 3, 'cartId' => 1]);

        list($json, $contentType) = $this->makeRequest('POST', '/cart/add', ['productId' => 4, 'cartId' => 1]);

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Cart product limit reached', $json);
    }

    public function testMaxCartProductQuantity() {
        list($json, $contentType) = $this->makeRequest('POST', '/cart/add', ['productId' => 1, 'cartId' => 1, 'quantity' => 11]);

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Max quantity of product reached', $json);
    }

    public function testDeleteCartProduct() {
        $this->makeRequest('POST', '/cart/add', ['productId' => 1, 'cartId' => 1]);

        list($json, $contentType) = $this->makeRequest('DELETE', '/cart/1/product/delete/1');

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Product removed from cart', $json);
    }

    public function testDeleteInvalidCartProduct() {
        list($json, $contentType) = $this->makeRequest('DELETE', '/cart/1/product/delete/66');

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Invalid params', $json);

        list($json, $contentType) = $this->makeRequest('DELETE', '/cart/3/product/delete/1');
        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Invalid params', $json);

        $this->expectException(NotFoundHttpException::class);
        $this->makeRequest('DELETE', '/cart/product/delete/xxx');
        $this->makeRequest('DELETE', '/cart/a/product/delete/xxx');
    }

    public function testList() {
        $this->makeRequest('POST', '/cart/add', ['productId' => 1, 'cartId' => 1]);
        $this->makeRequest('POST', '/cart/add', ['productId' => 2, 'cartId' => 1]);
        $this->makeRequest('POST', '/cart/add', ['productId' => 3, 'cartId' => 1]);

        list($json, $contentType) = $this->makeRequest('GET', '/cart/1');

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Fallout', $json);

        $content = json_decode($json);
        $totalSum = $content->total_sum;
        //1.99+2.99+3.99
        $this->assertSame(8.97, $totalSum);

        //add 10 product for 3.99
        $this->makeRequest('POST', '/cart/add', ['productId' => 3, 'cartId' => 1, 'quantity' => 9]);
        list($json, $contentType) = $this->makeRequest('GET', '/cart/1');
        $content = json_decode($json);
        $totalSum = $content->total_sum;
        //1.99+2.99+39.9
        $this->assertSame(44.88, $totalSum);
    }
}