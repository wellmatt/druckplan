<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaperPriceApiTest extends TestCase
{
    use MakePaperPriceTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePaperPrice()
    {
        $paperPrice = $this->fakePaperPriceData();
        $this->json('POST', '/api/v1/paperPrices', $paperPrice);

        $this->assertApiResponse($paperPrice);
    }

    /**
     * @test
     */
    public function testReadPaperPrice()
    {
        $paperPrice = $this->makePaperPrice();
        $this->json('GET', '/api/v1/paperPrices/'.$paperPrice->id);

        $this->assertApiResponse($paperPrice->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePaperPrice()
    {
        $paperPrice = $this->makePaperPrice();
        $editedPaperPrice = $this->fakePaperPriceData();

        $this->json('PUT', '/api/v1/paperPrices/'.$paperPrice->id, $editedPaperPrice);

        $this->assertApiResponse($editedPaperPrice);
    }

    /**
     * @test
     */
    public function testDeletePaperPrice()
    {
        $paperPrice = $this->makePaperPrice();
        $this->json('DELETE', '/api/v1/paperPrices/'.$paperPrice->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/paperPrices/'.$paperPrice->id);

        $this->assertResponseStatus(404);
    }
}
