<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MachineDifficultyApiTest extends TestCase
{
    use MakeMachineDifficultyTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateMachineDifficulty()
    {
        $machineDifficulty = $this->fakeMachineDifficultyData();
        $this->json('POST', '/api/v1/machineDifficulties', $machineDifficulty);

        $this->assertApiResponse($machineDifficulty);
    }

    /**
     * @test
     */
    public function testReadMachineDifficulty()
    {
        $machineDifficulty = $this->makeMachineDifficulty();
        $this->json('GET', '/api/v1/machineDifficulties/'.$machineDifficulty->id);

        $this->assertApiResponse($machineDifficulty->toArray());
    }

    /**
     * @test
     */
    public function testUpdateMachineDifficulty()
    {
        $machineDifficulty = $this->makeMachineDifficulty();
        $editedMachineDifficulty = $this->fakeMachineDifficultyData();

        $this->json('PUT', '/api/v1/machineDifficulties/'.$machineDifficulty->id, $editedMachineDifficulty);

        $this->assertApiResponse($editedMachineDifficulty);
    }

    /**
     * @test
     */
    public function testDeleteMachineDifficulty()
    {
        $machineDifficulty = $this->makeMachineDifficulty();
        $this->json('DELETE', '/api/v1/machineDifficulties/'.$machineDifficulty->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/machineDifficulties/'.$machineDifficulty->id);

        $this->assertResponseStatus(404);
    }
}
