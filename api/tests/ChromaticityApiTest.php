<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChromaticityApiTest extends TestCase
{
    use MakeChromaticityTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateChromaticity()
    {
        $chromaticity = $this->fakeChromaticityData();
        $this->json('POST', '/api/v1/chromaticities', $chromaticity);

        $this->assertApiResponse($chromaticity);
    }

    /**
     * @test
     */
    public function testReadChromaticity()
    {
        $chromaticity = $this->makeChromaticity();
        $this->json('GET', '/api/v1/chromaticities/'.$chromaticity->id);

        $this->assertApiResponse($chromaticity->toArray());
    }

    /**
     * @test
     */
    public function testUpdateChromaticity()
    {
        $chromaticity = $this->makeChromaticity();
        $editedChromaticity = $this->fakeChromaticityData();

        $this->json('PUT', '/api/v1/chromaticities/'.$chromaticity->id, $editedChromaticity);

        $this->assertApiResponse($editedChromaticity);
    }

    /**
     * @test
     */
    public function testDeleteChromaticity()
    {
        $chromaticity = $this->makeChromaticity();
        $this->json('DELETE', '/api/v1/chromaticities/'.$chromaticity->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/chromaticities/'.$chromaticity->id);

        $this->assertResponseStatus(404);
    }
}
