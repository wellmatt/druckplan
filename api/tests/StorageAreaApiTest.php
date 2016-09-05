<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StorageAreaApiTest extends TestCase
{
    use MakeStorageAreaTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateStorageArea()
    {
        $storageArea = $this->fakeStorageAreaData();
        $this->json('POST', '/api/v1/storageAreas', $storageArea);

        $this->assertApiResponse($storageArea);
    }

    /**
     * @test
     */
    public function testReadStorageArea()
    {
        $storageArea = $this->makeStorageArea();
        $this->json('GET', '/api/v1/storageAreas/'.$storageArea->id);

        $this->assertApiResponse($storageArea->toArray());
    }

    /**
     * @test
     */
    public function testUpdateStorageArea()
    {
        $storageArea = $this->makeStorageArea();
        $editedStorageArea = $this->fakeStorageAreaData();

        $this->json('PUT', '/api/v1/storageAreas/'.$storageArea->id, $editedStorageArea);

        $this->assertApiResponse($editedStorageArea);
    }

    /**
     * @test
     */
    public function testDeleteStorageArea()
    {
        $storageArea = $this->makeStorageArea();
        $this->json('DELETE', '/api/v1/storageAreas/'.$storageArea->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/storageAreas/'.$storageArea->id);

        $this->assertResponseStatus(404);
    }
}
