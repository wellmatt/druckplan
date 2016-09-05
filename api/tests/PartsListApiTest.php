<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PartsListApiTest extends TestCase
{
    use MakePartsListTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePartsList()
    {
        $partsList = $this->fakePartsListData();
        $this->json('POST', '/api/v1/partsLists', $partsList);

        $this->assertApiResponse($partsList);
    }

    /**
     * @test
     */
    public function testReadPartsList()
    {
        $partsList = $this->makePartsList();
        $this->json('GET', '/api/v1/partsLists/'.$partsList->id);

        $this->assertApiResponse($partsList->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePartsList()
    {
        $partsList = $this->makePartsList();
        $editedPartsList = $this->fakePartsListData();

        $this->json('PUT', '/api/v1/partsLists/'.$partsList->id, $editedPartsList);

        $this->assertApiResponse($editedPartsList);
    }

    /**
     * @test
     */
    public function testDeletePartsList()
    {
        $partsList = $this->makePartsList();
        $this->json('DELETE', '/api/v1/partsLists/'.$partsList->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/partsLists/'.$partsList->id);

        $this->assertResponseStatus(404);
    }
}
