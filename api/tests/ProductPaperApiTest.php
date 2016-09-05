<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductPaperApiTest extends TestCase
{
    use MakeProductPaperTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateProductPaper()
    {
        $productPaper = $this->fakeProductPaperData();
        $this->json('POST', '/api/v1/productPapers', $productPaper);

        $this->assertApiResponse($productPaper);
    }

    /**
     * @test
     */
    public function testReadProductPaper()
    {
        $productPaper = $this->makeProductPaper();
        $this->json('GET', '/api/v1/productPapers/'.$productPaper->id);

        $this->assertApiResponse($productPaper->toArray());
    }

    /**
     * @test
     */
    public function testUpdateProductPaper()
    {
        $productPaper = $this->makeProductPaper();
        $editedProductPaper = $this->fakeProductPaperData();

        $this->json('PUT', '/api/v1/productPapers/'.$productPaper->id, $editedProductPaper);

        $this->assertApiResponse($editedProductPaper);
    }

    /**
     * @test
     */
    public function testDeleteProductPaper()
    {
        $productPaper = $this->makeProductPaper();
        $this->json('DELETE', '/api/v1/productPapers/'.$productPaper->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/productPapers/'.$productPaper->id);

        $this->assertResponseStatus(404);
    }
}
