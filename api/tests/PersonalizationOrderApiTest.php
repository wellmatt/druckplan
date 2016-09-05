<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PersonalizationOrderApiTest extends TestCase
{
    use MakePersonalizationOrderTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePersonalizationOrder()
    {
        $personalizationOrder = $this->fakePersonalizationOrderData();
        $this->json('POST', '/api/v1/personalizationOrders', $personalizationOrder);

        $this->assertApiResponse($personalizationOrder);
    }

    /**
     * @test
     */
    public function testReadPersonalizationOrder()
    {
        $personalizationOrder = $this->makePersonalizationOrder();
        $this->json('GET', '/api/v1/personalizationOrders/'.$personalizationOrder->id);

        $this->assertApiResponse($personalizationOrder->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePersonalizationOrder()
    {
        $personalizationOrder = $this->makePersonalizationOrder();
        $editedPersonalizationOrder = $this->fakePersonalizationOrderData();

        $this->json('PUT', '/api/v1/personalizationOrders/'.$personalizationOrder->id, $editedPersonalizationOrder);

        $this->assertApiResponse($editedPersonalizationOrder);
    }

    /**
     * @test
     */
    public function testDeletePersonalizationOrder()
    {
        $personalizationOrder = $this->makePersonalizationOrder();
        $this->json('DELETE', '/api/v1/personalizationOrders/'.$personalizationOrder->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/personalizationOrders/'.$personalizationOrder->id);

        $this->assertResponseStatus(404);
    }
}
