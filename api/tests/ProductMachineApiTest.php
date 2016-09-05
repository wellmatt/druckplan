<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductMachineApiTest extends TestCase
{
    use MakeProductMachineTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateProductMachine()
    {
        $productMachine = $this->fakeProductMachineData();
        $this->json('POST', '/api/v1/productMachines', $productMachine);

        $this->assertApiResponse($productMachine);
    }

    /**
     * @test
     */
    public function testReadProductMachine()
    {
        $productMachine = $this->makeProductMachine();
        $this->json('GET', '/api/v1/productMachines/'.$productMachine->id);

        $this->assertApiResponse($productMachine->toArray());
    }

    /**
     * @test
     */
    public function testUpdateProductMachine()
    {
        $productMachine = $this->makeProductMachine();
        $editedProductMachine = $this->fakeProductMachineData();

        $this->json('PUT', '/api/v1/productMachines/'.$productMachine->id, $editedProductMachine);

        $this->assertApiResponse($editedProductMachine);
    }

    /**
     * @test
     */
    public function testDeleteProductMachine()
    {
        $productMachine = $this->makeProductMachine();
        $this->json('DELETE', '/api/v1/productMachines/'.$productMachine->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/productMachines/'.$productMachine->id);

        $this->assertResponseStatus(404);
    }
}
