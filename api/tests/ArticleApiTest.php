<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArticleApiTest extends TestCase
{
    use MakeArticleTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateArticle()
    {
        $article = $this->fakeArticleData();
        $this->json('POST', '/api/v1/articles', $article);

        $this->assertApiResponse($article);
    }

    /**
     * @test
     */
    public function testReadArticle()
    {
        $article = $this->makeArticle();
        $this->json('GET', '/api/v1/articles/'.$article->id);

        $this->assertApiResponse($article->toArray());
    }

    /**
     * @test
     */
    public function testUpdateArticle()
    {
        $article = $this->makeArticle();
        $editedArticle = $this->fakeArticleData();

        $this->json('PUT', '/api/v1/articles/'.$article->id, $editedArticle);

        $this->assertApiResponse($editedArticle);
    }

    /**
     * @test
     */
    public function testDeleteArticle()
    {
        $article = $this->makeArticle();
        $this->json('DELETE', '/api/v1/articles/'.$article->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/articles/'.$article->id);

        $this->assertResponseStatus(404);
    }
}
