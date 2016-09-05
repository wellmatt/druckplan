<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaperSizeApiTest extends TestCase
{
    use MakePaperSizeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePaperSize()
    {
        $paperSize = $this->fakePaperSizeData();
        $this->json('POST', '/api/v1/paperSizes', $paperSize);

        $this->assertApiResponse($paperSize);
    }

    /**
     * @test
     */
    public function testReadPaperSize()
    {
        $paperSize = $this->makePaperSize();
        $this->json('GET', '/api/v1/paperSizes/'.$paperSize->id);

        $this->assertApiResponse($paperSize->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePaperSize()
    {
        $paperSize = $this->makePaperSize();
        $editedPaperSize = $this->fakePaperSizeData();

        $this->json('PUT', '/api/v1/paperSizes/'.$paperSize->id, $editedPaperSize);

        $this->assertApiResponse($editedPaperSize);
    }

    /**
     * @test
     */
    public function testDeletePaperSize()
    {
        $paperSize = $this->makePaperSize();
        $this->json('DELETE', '/api/v1/paperSizes/'.$paperSize->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/paperSizes/'.$paperSize->id);

        $this->assertResponseStatus(404);
    }
}
