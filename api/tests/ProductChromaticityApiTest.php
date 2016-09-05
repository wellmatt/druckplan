<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductChromaticityApiTest extends TestCase
{
    use MakeProductChromaticityTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateProductChromaticity()
    {
        $productChromaticity = $this->fakeProductChromaticityData();
        $this->json('POST', '/api/v1/productChromaticities', $productChromaticity);

        $this->assertApiResponse($productChromaticity);
    }

    /**
     * @test
     */
    public function testReadProductChromaticity()
    {
        $productChromaticity = $this->makeProductChromaticity();
        $this->json('GET', '/api/v1/productChromaticities/'.$productChromaticity->id);

        $this->assertApiResponse($productChromaticity->toArray());
    }

    /**
     * @test
     */
    public function testUpdateProductChromaticity()
    {
        $productChromaticity = $this->makeProductChromaticity();
        $editedProductChromaticity = $this->fakeProductChromaticityData();

        $this->json('PUT', '/api/v1/productChromaticities/'.$productChromaticity->id, $editedProductChromaticity);

        $this->assertApiResponse($editedProductChromaticity);
    }

    /**
     * @test
     */
    public function testDeleteProductChromaticity()
    {
        $productChromaticity = $this->makeProductChromaticity();
        $this->json('DELETE', '/api/v1/productChromaticities/'.$productChromaticity->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/productChromaticities/'.$productChromaticity->id);

        $this->assertResponseStatus(404);
    }
}
