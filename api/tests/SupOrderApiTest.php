<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupOrderApiTest extends TestCase
{
    use MakeSupOrderTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSupOrder()
    {
        $supOrder = $this->fakeSupOrderData();
        $this->json('POST', '/api/v1/supOrders', $supOrder);

        $this->assertApiResponse($supOrder);
    }

    /**
     * @test
     */
    public function testReadSupOrder()
    {
        $supOrder = $this->makeSupOrder();
        $this->json('GET', '/api/v1/supOrders/'.$supOrder->id);

        $this->assertApiResponse($supOrder->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSupOrder()
    {
        $supOrder = $this->makeSupOrder();
        $editedSupOrder = $this->fakeSupOrderData();

        $this->json('PUT', '/api/v1/supOrders/'.$supOrder->id, $editedSupOrder);

        $this->assertApiResponse($editedSupOrder);
    }

    /**
     * @test
     */
    public function testDeleteSupOrder()
    {
        $supOrder = $this->makeSupOrder();
        $this->json('DELETE', '/api/v1/supOrders/'.$supOrder->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/supOrders/'.$supOrder->id);

        $this->assertResponseStatus(404);
    }
}
