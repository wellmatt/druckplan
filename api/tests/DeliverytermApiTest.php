<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeliverytermApiTest extends TestCase
{
    use MakeDeliverytermTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDeliveryterm()
    {
        $deliveryterm = $this->fakeDeliverytermData();
        $this->json('POST', '/api/v1/deliveryterms', $deliveryterm);

        $this->assertApiResponse($deliveryterm);
    }

    /**
     * @test
     */
    public function testReadDeliveryterm()
    {
        $deliveryterm = $this->makeDeliveryterm();
        $this->json('GET', '/api/v1/deliveryterms/'.$deliveryterm->id);

        $this->assertApiResponse($deliveryterm->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDeliveryterm()
    {
        $deliveryterm = $this->makeDeliveryterm();
        $editedDeliveryterm = $this->fakeDeliverytermData();

        $this->json('PUT', '/api/v1/deliveryterms/'.$deliveryterm->id, $editedDeliveryterm);

        $this->assertApiResponse($editedDeliveryterm);
    }

    /**
     * @test
     */
    public function testDeleteDeliveryterm()
    {
        $deliveryterm = $this->makeDeliveryterm();
        $this->json('DELETE', '/api/v1/deliveryterms/'.$deliveryterm->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/deliveryterms/'.$deliveryterm->id);

        $this->assertResponseStatus(404);
    }
}
