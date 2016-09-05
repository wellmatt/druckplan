<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StorageBookEntryApiTest extends TestCase
{
    use MakeStorageBookEntryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateStorageBookEntry()
    {
        $storageBookEntry = $this->fakeStorageBookEntryData();
        $this->json('POST', '/api/v1/storageBookEntries', $storageBookEntry);

        $this->assertApiResponse($storageBookEntry);
    }

    /**
     * @test
     */
    public function testReadStorageBookEntry()
    {
        $storageBookEntry = $this->makeStorageBookEntry();
        $this->json('GET', '/api/v1/storageBookEntries/'.$storageBookEntry->id);

        $this->assertApiResponse($storageBookEntry->toArray());
    }

    /**
     * @test
     */
    public function testUpdateStorageBookEntry()
    {
        $storageBookEntry = $this->makeStorageBookEntry();
        $editedStorageBookEntry = $this->fakeStorageBookEntryData();

        $this->json('PUT', '/api/v1/storageBookEntries/'.$storageBookEntry->id, $editedStorageBookEntry);

        $this->assertApiResponse($editedStorageBookEntry);
    }

    /**
     * @test
     */
    public function testDeleteStorageBookEntry()
    {
        $storageBookEntry = $this->makeStorageBookEntry();
        $this->json('DELETE', '/api/v1/storageBookEntries/'.$storageBookEntry->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/storageBookEntries/'.$storageBookEntry->id);

        $this->assertResponseStatus(404);
    }
}
