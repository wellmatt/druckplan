<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArticleShopApprovalApiTest extends TestCase
{
    use MakeArticleShopApprovalTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateArticleShopApproval()
    {
        $articleShopApproval = $this->fakeArticleShopApprovalData();
        $this->json('POST', '/api/v1/articleShopApprovals', $articleShopApproval);

        $this->assertApiResponse($articleShopApproval);
    }

    /**
     * @test
     */
    public function testReadArticleShopApproval()
    {
        $articleShopApproval = $this->makeArticleShopApproval();
        $this->json('GET', '/api/v1/articleShopApprovals/'.$articleShopApproval->id);

        $this->assertApiResponse($articleShopApproval->toArray());
    }

    /**
     * @test
     */
    public function testUpdateArticleShopApproval()
    {
        $articleShopApproval = $this->makeArticleShopApproval();
        $editedArticleShopApproval = $this->fakeArticleShopApprovalData();

        $this->json('PUT', '/api/v1/articleShopApprovals/'.$articleShopApproval->id, $editedArticleShopApproval);

        $this->assertApiResponse($editedArticleShopApproval);
    }

    /**
     * @test
     */
    public function testDeleteArticleShopApproval()
    {
        $articleShopApproval = $this->makeArticleShopApproval();
        $this->json('DELETE', '/api/v1/articleShopApprovals/'.$articleShopApproval->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/articleShopApprovals/'.$articleShopApproval->id);

        $this->assertResponseStatus(404);
    }
}
