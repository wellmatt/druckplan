<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MachineLockApiTest extends TestCase
{
    use MakeMachineLockTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateMachineLock()
    {
        $machineLock = $this->fakeMachineLockData();
        $this->json('POST', '/api/v1/machineLocks', $machineLock);

        $this->assertApiResponse($machineLock);
    }

    /**
     * @test
     */
    public function testReadMachineLock()
    {
        $machineLock = $this->makeMachineLock();
        $this->json('GET', '/api/v1/machineLocks/'.$machineLock->id);

        $this->assertApiResponse($machineLock->toArray());
    }

    /**
     * @test
     */
    public function testUpdateMachineLock()
    {
        $machineLock = $this->makeMachineLock();
        $editedMachineLock = $this->fakeMachineLockData();

        $this->json('PUT', '/api/v1/machineLocks/'.$machineLock->id, $editedMachineLock);

        $this->assertApiResponse($editedMachineLock);
    }

    /**
     * @test
     */
    public function testDeleteMachineLock()
    {
        $machineLock = $this->makeMachineLock();
        $this->json('DELETE', '/api/v1/machineLocks/'.$machineLock->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/machineLocks/'.$machineLock->id);

        $this->assertResponseStatus(404);
    }
}
