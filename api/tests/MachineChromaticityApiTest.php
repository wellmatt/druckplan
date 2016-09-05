<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MachineChromaticityApiTest extends TestCase
{
    use MakeMachineChromaticityTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateMachineChromaticity()
    {
        $machineChromaticity = $this->fakeMachineChromaticityData();
        $this->json('POST', '/api/v1/machineChromaticities', $machineChromaticity);

        $this->assertApiResponse($machineChromaticity);
    }

    /**
     * @test
     */
    public function testReadMachineChromaticity()
    {
        $machineChromaticity = $this->makeMachineChromaticity();
        $this->json('GET', '/api/v1/machineChromaticities/'.$machineChromaticity->id);

        $this->assertApiResponse($machineChromaticity->toArray());
    }

    /**
     * @test
     */
    public function testUpdateMachineChromaticity()
    {
        $machineChromaticity = $this->makeMachineChromaticity();
        $editedMachineChromaticity = $this->fakeMachineChromaticityData();

        $this->json('PUT', '/api/v1/machineChromaticities/'.$machineChromaticity->id, $editedMachineChromaticity);

        $this->assertApiResponse($editedMachineChromaticity);
    }

    /**
     * @test
     */
    public function testDeleteMachineChromaticity()
    {
        $machineChromaticity = $this->makeMachineChromaticity();
        $this->json('DELETE', '/api/v1/machineChromaticities/'.$machineChromaticity->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/machineChromaticities/'.$machineChromaticity->id);

        $this->assertResponseStatus(404);
    }
}
