<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductFormatApiTest extends TestCase
{
    use MakeProductFormatTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateProductFormat()
    {
        $productFormat = $this->fakeProductFormatData();
        $this->json('POST', '/api/v1/productFormats', $productFormat);

        $this->assertApiResponse($productFormat);
    }

    /**
     * @test
     */
    public function testReadProductFormat()
    {
        $productFormat = $this->makeProductFormat();
        $this->json('GET', '/api/v1/productFormats/'.$productFormat->id);

        $this->assertApiResponse($productFormat->toArray());
    }

    /**
     * @test
     */
    public function testUpdateProductFormat()
    {
        $productFormat = $this->makeProductFormat();
        $editedProductFormat = $this->fakeProductFormatData();

        $this->json('PUT', '/api/v1/productFormats/'.$productFormat->id, $editedProductFormat);

        $this->assertApiResponse($editedProductFormat);
    }

    /**
     * @test
     */
    public function testDeleteProductFormat()
    {
        $productFormat = $this->makeProductFormat();
        $this->json('DELETE', '/api/v1/productFormats/'.$productFormat->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/productFormats/'.$productFormat->id);

        $this->assertResponseStatus(404);
    }
}
