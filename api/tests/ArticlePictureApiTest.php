<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArticlePictureApiTest extends TestCase
{
    use MakeArticlePictureTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateArticlePicture()
    {
        $articlePicture = $this->fakeArticlePictureData();
        $this->json('POST', '/api/v1/articlePictures', $articlePicture);

        $this->assertApiResponse($articlePicture);
    }

    /**
     * @test
     */
    public function testReadArticlePicture()
    {
        $articlePicture = $this->makeArticlePicture();
        $this->json('GET', '/api/v1/articlePictures/'.$articlePicture->id);

        $this->assertApiResponse($articlePicture->toArray());
    }

    /**
     * @test
     */
    public function testUpdateArticlePicture()
    {
        $articlePicture = $this->makeArticlePicture();
        $editedArticlePicture = $this->fakeArticlePictureData();

        $this->json('PUT', '/api/v1/articlePictures/'.$articlePicture->id, $editedArticlePicture);

        $this->assertApiResponse($editedArticlePicture);
    }

    /**
     * @test
     */
    public function testDeleteArticlePicture()
    {
        $articlePicture = $this->makeArticlePicture();
        $this->json('DELETE', '/api/v1/articlePictures/'.$articlePicture->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/articlePictures/'.$articlePicture->id);

        $this->assertResponseStatus(404);
    }
}
