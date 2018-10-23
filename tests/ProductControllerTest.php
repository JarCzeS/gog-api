<?php

namespace App\Tests;


use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductControllerTest extends DataFixtureTestCase
{
    public function setUp() {
        parent::setUp();
    }

    public function testTrashRequest() {
        list($json, $contentType) = $this->makeRequest('GET', '/products');

        list($noPageResponse, $contentType) = $this->makeRequest('GET', '/products/%@@@EDD');

        $this->assertSame($json, $noPageResponse);
        $this->assertEquals("application/json", $contentType);

        list($noDataResponse, $contentType) = $this->makeRequest('GET', '/products/99999');

        $this->assertSame([], json_decode($noDataResponse));
        $this->assertEquals("application/json", $contentType);
    }

    public function testProductListing() {
        list($json, $contentType) = $this->makeRequest('GET', '/products');
        $content = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(3, count($content));
        $this->assertContains('Fallout', $json);
        $this->assertContains('Baldur\'s Gate', $json);
    }

    public function testProductListingSecondPage() {
        list($json, $contentType) = $this->makeRequest('GET', '/products/2');
        $content = json_decode($json);

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(2, count($content));
        $this->assertContains('Bloodborne', $json);
        $this->assertContains('5.99', $json);

    }

    public function testAddNotUniqueProductTitle() {
        list($json, $contentType) = $this->makeRequest('POST', '/product/add',['title'=>'Fallout', 'price'=> '1.99']);

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Title already exists', $json);
    }

    public function testAddProduct() {
        list($json, $contentType) = $this->makeRequest('POST', '/product/add', ['title' => 'Overwatch', 'price' => 9.87]);

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Product added', $json);
    }

    public function testAddProductInvalidParameters() {
        list($json, $contentType) = $this->makeRequest('POST', '/product/add', ['price' => 0]);

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Invalid values', $json);

        $this->makeRequest('POST', '/product/add');
        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());

        $this->expectException(NotFoundHttpException::class);
        $this->makeRequest('POST', '/product/add/1');
    }

    public function testDeleteProduct() {
        list($json, $contentType) = $this->makeRequest('DELETE', '/product/delete/1');

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Product deleted', $json);
    }

    public function testDeleteInvalidParamProduct() {
        list($json, $contentType) = $this->makeRequest('DELETE', '/product/delete/xxa3#$@');

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertContains('No product found', $json);

        $this->expectException(NotFoundHttpException::class);

        $this->makeRequest('DELETE', '/product/delete');
    }

    public function testUpdateNotUniqueProductTitle() {
        list($json, $contentType) = $this->makeRequest('PUT', '/product/update/1',['title'=>'Bloodborne']);

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Title already exists', $json);
    }

    public function testUpdateProduct() {
        list($json, $contentType) = $this->makeRequest('PUT', '/product/update/1', ['title' => 'Fallout New Vegas ', 'price' => 3.87]);

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Product changed', $json);

        list($json, $contentType) = $this->makeRequest('GET', '/products');
        $this->assertContains('New Vegas', $json);
    }

    public function testUpdateProductInvalidParameters() {
        list($json, $contentType) = $this->makeRequest('PUT', '/product/update/1', ['price' => 0]);

        $this->assertEquals("application/json", $contentType);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Invalid params', $json);

        $this->expectException(NotFoundHttpException::class);
        $this->makeRequest('PUT', '/product/update');
    }

    /**
     * @dataProvider paginationProvider
     * @param $page
     * @param $expectedOffset
     */
    public function testPaginationOffset($page, $expectedOffset) {
        $productsRepository = $this->entityManager->getRepository(Product::class);

        $this->assertSame($expectedOffset, $productsRepository->countOffset($page));
    }

    public function paginationProvider()
    {
        return [
            'page 1'  => [1,0],
            'page 2' => [2, 3],
            'page 3' => [3, 6],
        ];
    }
}
