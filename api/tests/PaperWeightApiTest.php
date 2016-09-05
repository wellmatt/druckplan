<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaperWeightApiTest extends TestCase
{
    use MakePaperWeightTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePaperWeight()
    {
        $paperWeight = $this->fakePaperWeightData();
        $this->json('POST', '/api/v1/paperWeights', $paperWeight);

        $this->assertApiResponse($paperWeight);
    }

    /**
     * @test
     */
    public function testReadPaperWeight()
    {
        $paperWeight = $this->makePaperWeight();
        $this->json('GET', '/api/v1/paperWeights/'.$paperWeight->id);

        $this->assertApiResponse($paperWeight->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePaperWeight()
    {
        $paperWeight = $this->makePaperWeight();
        $editedPaperWeight = $this->fakePaperWeightData();

        $this->json('PUT', '/api/v1/paperWeights/'.$paperWeight->id, $editedPaperWeight);

        $this->assertApiResponse($editedPaperWeight);
    }

    /**
     * @test
     */
    public function testDeletePaperWeight()
    {
        $paperWeight = $this->makePaperWeight();
        $this->json('DELETE', '/api/v1/paperWeights/'.$paperWeight->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/paperWeights/'.$paperWeight->id);

        $this->assertResponseStatus(404);
    }
}
