<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderMachineApiTest extends TestCase
{
    use MakeOrderMachineTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateOrderMachine()
    {
        $orderMachine = $this->fakeOrderMachineData();
        $this->json('POST', '/api/v1/orderMachines', $orderMachine);

        $this->assertApiResponse($orderMachine);
    }

    /**
     * @test
     */
    public function testReadOrderMachine()
    {
        $orderMachine = $this->makeOrderMachine();
        $this->json('GET', '/api/v1/orderMachines/'.$orderMachine->id);

        $this->assertApiResponse($orderMachine->toArray());
    }

    /**
     * @test
     */
    public function testUpdateOrderMachine()
    {
        $orderMachine = $this->makeOrderMachine();
        $editedOrderMachine = $this->fakeOrderMachineData();

        $this->json('PUT', '/api/v1/orderMachines/'.$orderMachine->id, $editedOrderMachine);

        $this->assertApiResponse($editedOrderMachine);
    }

    /**
     * @test
     */
    public function testDeleteOrderMachine()
    {
        $orderMachine = $this->makeOrderMachine();
        $this->json('DELETE', '/api/v1/orderMachines/'.$orderMachine->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/orderMachines/'.$orderMachine->id);

        $this->assertResponseStatus(404);
    }
}
