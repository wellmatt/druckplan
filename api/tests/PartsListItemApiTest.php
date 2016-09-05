<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PartsListItemApiTest extends TestCase
{
    use MakePartsListItemTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePartsListItem()
    {
        $partsListItem = $this->fakePartsListItemData();
        $this->json('POST', '/api/v1/partsListItems', $partsListItem);

        $this->assertApiResponse($partsListItem);
    }

    /**
     * @test
     */
    public function testReadPartsListItem()
    {
        $partsListItem = $this->makePartsListItem();
        $this->json('GET', '/api/v1/partsListItems/'.$partsListItem->id);

        $this->assertApiResponse($partsListItem->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePartsListItem()
    {
        $partsListItem = $this->makePartsListItem();
        $editedPartsListItem = $this->fakePartsListItemData();

        $this->json('PUT', '/api/v1/partsListItems/'.$partsListItem->id, $editedPartsListItem);

        $this->assertApiResponse($editedPartsListItem);
    }

    /**
     * @test
     */
    public function testDeletePartsListItem()
    {
        $partsListItem = $this->makePartsListItem();
        $this->json('DELETE', '/api/v1/partsListItems/'.$partsListItem->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/partsListItems/'.$partsListItem->id);

        $this->assertResponseStatus(404);
    }
}
