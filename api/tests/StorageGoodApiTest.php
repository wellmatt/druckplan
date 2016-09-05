<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StorageGoodApiTest extends TestCase
{
    use MakeStorageGoodTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateStorageGood()
    {
        $storageGood = $this->fakeStorageGoodData();
        $this->json('POST', '/api/v1/storageGoods', $storageGood);

        $this->assertApiResponse($storageGood);
    }

    /**
     * @test
     */
    public function testReadStorageGood()
    {
        $storageGood = $this->makeStorageGood();
        $this->json('GET', '/api/v1/storageGoods/'.$storageGood->id);

        $this->assertApiResponse($storageGood->toArray());
    }

    /**
     * @test
     */
    public function testUpdateStorageGood()
    {
        $storageGood = $this->makeStorageGood();
        $editedStorageGood = $this->fakeStorageGoodData();

        $this->json('PUT', '/api/v1/storageGoods/'.$storageGood->id, $editedStorageGood);

        $this->assertApiResponse($editedStorageGood);
    }

    /**
     * @test
     */
    public function testDeleteStorageGood()
    {
        $storageGood = $this->makeStorageGood();
        $this->json('DELETE', '/api/v1/storageGoods/'.$storageGood->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/storageGoods/'.$storageGood->id);

        $this->assertResponseStatus(404);
    }
}
