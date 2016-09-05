<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PersonalizationOrderItemApiTest extends TestCase
{
    use MakePersonalizationOrderItemTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePersonalizationOrderItem()
    {
        $personalizationOrderItem = $this->fakePersonalizationOrderItemData();
        $this->json('POST', '/api/v1/personalizationOrderItems', $personalizationOrderItem);

        $this->assertApiResponse($personalizationOrderItem);
    }

    /**
     * @test
     */
    public function testReadPersonalizationOrderItem()
    {
        $personalizationOrderItem = $this->makePersonalizationOrderItem();
        $this->json('GET', '/api/v1/personalizationOrderItems/'.$personalizationOrderItem->id);

        $this->assertApiResponse($personalizationOrderItem->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePersonalizationOrderItem()
    {
        $personalizationOrderItem = $this->makePersonalizationOrderItem();
        $editedPersonalizationOrderItem = $this->fakePersonalizationOrderItemData();

        $this->json('PUT', '/api/v1/personalizationOrderItems/'.$personalizationOrderItem->id, $editedPersonalizationOrderItem);

        $this->assertApiResponse($editedPersonalizationOrderItem);
    }

    /**
     * @test
     */
    public function testDeletePersonalizationOrderItem()
    {
        $personalizationOrderItem = $this->makePersonalizationOrderItem();
        $this->json('DELETE', '/api/v1/personalizationOrderItems/'.$personalizationOrderItem->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/personalizationOrderItems/'.$personalizationOrderItem->id);

        $this->assertResponseStatus(404);
    }
}
