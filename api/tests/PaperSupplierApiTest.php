<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaperSupplierApiTest extends TestCase
{
    use MakePaperSupplierTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePaperSupplier()
    {
        $paperSupplier = $this->fakePaperSupplierData();
        $this->json('POST', '/api/v1/paperSuppliers', $paperSupplier);

        $this->assertApiResponse($paperSupplier);
    }

    /**
     * @test
     */
    public function testReadPaperSupplier()
    {
        $paperSupplier = $this->makePaperSupplier();
        $this->json('GET', '/api/v1/paperSuppliers/'.$paperSupplier->id);

        $this->assertApiResponse($paperSupplier->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePaperSupplier()
    {
        $paperSupplier = $this->makePaperSupplier();
        $editedPaperSupplier = $this->fakePaperSupplierData();

        $this->json('PUT', '/api/v1/paperSuppliers/'.$paperSupplier->id, $editedPaperSupplier);

        $this->assertApiResponse($editedPaperSupplier);
    }

    /**
     * @test
     */
    public function testDeletePaperSupplier()
    {
        $paperSupplier = $this->makePaperSupplier();
        $this->json('DELETE', '/api/v1/paperSuppliers/'.$paperSupplier->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/paperSuppliers/'.$paperSupplier->id);

        $this->assertResponseStatus(404);
    }
}
