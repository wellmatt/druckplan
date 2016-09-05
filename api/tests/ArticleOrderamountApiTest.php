<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArticleOrderamountApiTest extends TestCase
{
    use MakeArticleOrderamountTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateArticleOrderamount()
    {
        $articleOrderamount = $this->fakeArticleOrderamountData();
        $this->json('POST', '/api/v1/articleOrderamounts', $articleOrderamount);

        $this->assertApiResponse($articleOrderamount);
    }

    /**
     * @test
     */
    public function testReadArticleOrderamount()
    {
        $articleOrderamount = $this->makeArticleOrderamount();
        $this->json('GET', '/api/v1/articleOrderamounts/'.$articleOrderamount->id);

        $this->assertApiResponse($articleOrderamount->toArray());
    }

    /**
     * @test
     */
    public function testUpdateArticleOrderamount()
    {
        $articleOrderamount = $this->makeArticleOrderamount();
        $editedArticleOrderamount = $this->fakeArticleOrderamountData();

        $this->json('PUT', '/api/v1/articleOrderamounts/'.$articleOrderamount->id, $editedArticleOrderamount);

        $this->assertApiResponse($editedArticleOrderamount);
    }

    /**
     * @test
     */
    public function testDeleteArticleOrderamount()
    {
        $articleOrderamount = $this->makeArticleOrderamount();
        $this->json('DELETE', '/api/v1/articleOrderamounts/'.$articleOrderamount->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/articleOrderamounts/'.$articleOrderamount->id);

        $this->assertResponseStatus(404);
    }
}
