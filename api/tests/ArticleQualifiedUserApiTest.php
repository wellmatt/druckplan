<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArticleQualifiedUserApiTest extends TestCase
{
    use MakeArticleQualifiedUserTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateArticleQualifiedUser()
    {
        $articleQualifiedUser = $this->fakeArticleQualifiedUserData();
        $this->json('POST', '/api/v1/articleQualifiedUsers', $articleQualifiedUser);

        $this->assertApiResponse($articleQualifiedUser);
    }

    /**
     * @test
     */
    public function testReadArticleQualifiedUser()
    {
        $articleQualifiedUser = $this->makeArticleQualifiedUser();
        $this->json('GET', '/api/v1/articleQualifiedUsers/'.$articleQualifiedUser->id);

        $this->assertApiResponse($articleQualifiedUser->toArray());
    }

    /**
     * @test
     */
    public function testUpdateArticleQualifiedUser()
    {
        $articleQualifiedUser = $this->makeArticleQualifiedUser();
        $editedArticleQualifiedUser = $this->fakeArticleQualifiedUserData();

        $this->json('PUT', '/api/v1/articleQualifiedUsers/'.$articleQualifiedUser->id, $editedArticleQualifiedUser);

        $this->assertApiResponse($editedArticleQualifiedUser);
    }

    /**
     * @test
     */
    public function testDeleteArticleQualifiedUser()
    {
        $articleQualifiedUser = $this->makeArticleQualifiedUser();
        $this->json('DELETE', '/api/v1/articleQualifiedUsers/'.$articleQualifiedUser->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/articleQualifiedUsers/'.$articleQualifiedUser->id);

        $this->assertResponseStatus(404);
    }
}
