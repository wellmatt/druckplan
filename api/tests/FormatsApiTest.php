<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FormatsApiTest extends TestCase
{
    use MakeFormatsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFormats()
    {
        $formats = $this->fakeFormatsData();
        $this->json('POST', '/api/v1/formats', $formats);

        $this->assertApiResponse($formats);
    }

    /**
     * @test
     */
    public function testReadFormats()
    {
        $formats = $this->makeFormats();
        $this->json('GET', '/api/v1/formats/'.$formats->id);

        $this->assertApiResponse($formats->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFormats()
    {
        $formats = $this->makeFormats();
        $editedFormats = $this->fakeFormatsData();

        $this->json('PUT', '/api/v1/formats/'.$formats->id, $editedFormats);

        $this->assertApiResponse($editedFormats);
    }

    /**
     * @test
     */
    public function testDeleteFormats()
    {
        $formats = $this->makeFormats();
        $this->json('DELETE', '/api/v1/formats/'.$formats->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/formats/'.$formats->id);

        $this->assertResponseStatus(404);
    }
}
