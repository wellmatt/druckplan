<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArticleTagApiTest extends TestCase
{
    use MakeArticleTagTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateArticleTag()
    {
        $articleTag = $this->fakeArticleTagData();
        $this->json('POST', '/api/v1/articleTags', $articleTag);

        $this->assertApiResponse($articleTag);
    }

    /**
     * @test
     */
    public function testReadArticleTag()
    {
        $articleTag = $this->makeArticleTag();
        $this->json('GET', '/api/v1/articleTags/'.$articleTag->id);

        $this->assertApiResponse($articleTag->toArray());
    }

    /**
     * @test
     */
    public function testUpdateArticleTag()
    {
        $articleTag = $this->makeArticleTag();
        $editedArticleTag = $this->fakeArticleTagData();

        $this->json('PUT', '/api/v1/articleTags/'.$articleTag->id, $editedArticleTag);

        $this->assertApiResponse($editedArticleTag);
    }

    /**
     * @test
     */
    public function testDeleteArticleTag()
    {
        $articleTag = $this->makeArticleTag();
        $this->json('DELETE', '/api/v1/articleTags/'.$articleTag->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/articleTags/'.$articleTag->id);

        $this->assertResponseStatus(404);
    }
}
