<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StoragePositionApiTest extends TestCase
{
    use MakeStoragePositionTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateStoragePosition()
    {
        $storagePosition = $this->fakeStoragePositionData();
        $this->json('POST', '/api/v1/storagePositions', $storagePosition);

        $this->assertApiResponse($storagePosition);
    }

    /**
     * @test
     */
    public function testReadStoragePosition()
    {
        $storagePosition = $this->makeStoragePosition();
        $this->json('GET', '/api/v1/storagePositions/'.$storagePosition->id);

        $this->assertApiResponse($storagePosition->toArray());
    }

    /**
     * @test
     */
    public function testUpdateStoragePosition()
    {
        $storagePosition = $this->makeStoragePosition();
        $editedStoragePosition = $this->fakeStoragePositionData();

        $this->json('PUT', '/api/v1/storagePositions/'.$storagePosition->id, $editedStoragePosition);

        $this->assertApiResponse($editedStoragePosition);
    }

    /**
     * @test
     */
    public function testDeleteStoragePosition()
    {
        $storagePosition = $this->makeStoragePosition();
        $this->json('DELETE', '/api/v1/storagePositions/'.$storagePosition->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/storagePositions/'.$storagePosition->id);

        $this->assertResponseStatus(404);
    }
}
