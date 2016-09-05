<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaperApiTest extends TestCase
{
    use MakePaperTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePaper()
    {
        $paper = $this->fakePaperData();
        $this->json('POST', '/api/v1/papers', $paper);

        $this->assertApiResponse($paper);
    }

    /**
     * @test
     */
    public function testReadPaper()
    {
        $paper = $this->makePaper();
        $this->json('GET', '/api/v1/papers/'.$paper->id);

        $this->assertApiResponse($paper->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePaper()
    {
        $paper = $this->makePaper();
        $editedPaper = $this->fakePaperData();

        $this->json('PUT', '/api/v1/papers/'.$paper->id, $editedPaper);

        $this->assertApiResponse($editedPaper);
    }

    /**
     * @test
     */
    public function testDeletePaper()
    {
        $paper = $this->makePaper();
        $this->json('DELETE', '/api/v1/papers/'.$paper->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/papers/'.$paper->id);

        $this->assertResponseStatus(404);
    }
}
