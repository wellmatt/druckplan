<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StorageGoodPositionApiTest extends TestCase
{
    use MakeStorageGoodPositionTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateStorageGoodPosition()
    {
        $storageGoodPosition = $this->fakeStorageGoodPositionData();
        $this->json('POST', '/api/v1/storageGoodPositions', $storageGoodPosition);

        $this->assertApiResponse($storageGoodPosition);
    }

    /**
     * @test
     */
    public function testReadStorageGoodPosition()
    {
        $storageGoodPosition = $this->makeStorageGoodPosition();
        $this->json('GET', '/api/v1/storageGoodPositions/'.$storageGoodPosition->id);

        $this->assertApiResponse($storageGoodPosition->toArray());
    }

    /**
     * @test
     */
    public function testUpdateStorageGoodPosition()
    {
        $storageGoodPosition = $this->makeStorageGoodPosition();
        $editedStorageGoodPosition = $this->fakeStorageGoodPositionData();

        $this->json('PUT', '/api/v1/storageGoodPositions/'.$storageGoodPosition->id, $editedStorageGoodPosition);

        $this->assertApiResponse($editedStorageGoodPosition);
    }

    /**
     * @test
     */
    public function testDeleteStorageGoodPosition()
    {
        $storageGoodPosition = $this->makeStorageGoodPosition();
        $this->json('DELETE', '/api/v1/storageGoodPositions/'.$storageGoodPosition->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/storageGoodPositions/'.$storageGoodPosition->id);

        $this->assertResponseStatus(404);
    }
}
