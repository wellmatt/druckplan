<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderCalculationApiTest extends TestCase
{
    use MakeOrderCalculationTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateOrderCalculation()
    {
        $orderCalculation = $this->fakeOrderCalculationData();
        $this->json('POST', '/api/v1/orderCalculations', $orderCalculation);

        $this->assertApiResponse($orderCalculation);
    }

    /**
     * @test
     */
    public function testReadOrderCalculation()
    {
        $orderCalculation = $this->makeOrderCalculation();
        $this->json('GET', '/api/v1/orderCalculations/'.$orderCalculation->id);

        $this->assertApiResponse($orderCalculation->toArray());
    }

    /**
     * @test
     */
    public function testUpdateOrderCalculation()
    {
        $orderCalculation = $this->makeOrderCalculation();
        $editedOrderCalculation = $this->fakeOrderCalculationData();

        $this->json('PUT', '/api/v1/orderCalculations/'.$orderCalculation->id, $editedOrderCalculation);

        $this->assertApiResponse($editedOrderCalculation);
    }

    /**
     * @test
     */
    public function testDeleteOrderCalculation()
    {
        $orderCalculation = $this->makeOrderCalculation();
        $this->json('DELETE', '/api/v1/orderCalculations/'.$orderCalculation->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/orderCalculations/'.$orderCalculation->id);

        $this->assertResponseStatus(404);
    }
}
