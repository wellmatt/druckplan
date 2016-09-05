<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArticlePricescaleApiTest extends TestCase
{
    use MakeArticlePricescaleTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateArticlePricescale()
    {
        $articlePricescale = $this->fakeArticlePricescaleData();
        $this->json('POST', '/api/v1/articlePricescales', $articlePricescale);

        $this->assertApiResponse($articlePricescale);
    }

    /**
     * @test
     */
    public function testReadArticlePricescale()
    {
        $articlePricescale = $this->makeArticlePricescale();
        $this->json('GET', '/api/v1/articlePricescales/'.$articlePricescale->id);

        $this->assertApiResponse($articlePricescale->toArray());
    }

    /**
     * @test
     */
    public function testUpdateArticlePricescale()
    {
        $articlePricescale = $this->makeArticlePricescale();
        $editedArticlePricescale = $this->fakeArticlePricescaleData();

        $this->json('PUT', '/api/v1/articlePricescales/'.$articlePricescale->id, $editedArticlePricescale);

        $this->assertApiResponse($editedArticlePricescale);
    }

    /**
     * @test
     */
    public function testDeleteArticlePricescale()
    {
        $articlePricescale = $this->makeArticlePricescale();
        $this->json('DELETE', '/api/v1/articlePricescales/'.$articlePricescale->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/articlePricescales/'.$articlePricescale->id);

        $this->assertResponseStatus(404);
    }
}
