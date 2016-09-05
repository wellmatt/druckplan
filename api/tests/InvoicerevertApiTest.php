<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InvoicerevertApiTest extends TestCase
{
    use MakeInvoicerevertTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateInvoicerevert()
    {
        $invoicerevert = $this->fakeInvoicerevertData();
        $this->json('POST', '/api/v1/invoicereverts', $invoicerevert);

        $this->assertApiResponse($invoicerevert);
    }

    /**
     * @test
     */
    public function testReadInvoicerevert()
    {
        $invoicerevert = $this->makeInvoicerevert();
        $this->json('GET', '/api/v1/invoicereverts/'.$invoicerevert->id);

        $this->assertApiResponse($invoicerevert->toArray());
    }

    /**
     * @test
     */
    public function testUpdateInvoicerevert()
    {
        $invoicerevert = $this->makeInvoicerevert();
        $editedInvoicerevert = $this->fakeInvoicerevertData();

        $this->json('PUT', '/api/v1/invoicereverts/'.$invoicerevert->id, $editedInvoicerevert);

        $this->assertApiResponse($editedInvoicerevert);
    }

    /**
     * @test
     */
    public function testDeleteInvoicerevert()
    {
        $invoicerevert = $this->makeInvoicerevert();
        $this->json('DELETE', '/api/v1/invoicereverts/'.$invoicerevert->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/invoicereverts/'.$invoicerevert->id);

        $this->assertResponseStatus(404);
    }
}
