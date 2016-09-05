<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InvoicetemplateApiTest extends TestCase
{
    use MakeInvoicetemplateTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateInvoicetemplate()
    {
        $invoicetemplate = $this->fakeInvoicetemplateData();
        $this->json('POST', '/api/v1/invoicetemplates', $invoicetemplate);

        $this->assertApiResponse($invoicetemplate);
    }

    /**
     * @test
     */
    public function testReadInvoicetemplate()
    {
        $invoicetemplate = $this->makeInvoicetemplate();
        $this->json('GET', '/api/v1/invoicetemplates/'.$invoicetemplate->id);

        $this->assertApiResponse($invoicetemplate->toArray());
    }

    /**
     * @test
     */
    public function testUpdateInvoicetemplate()
    {
        $invoicetemplate = $this->makeInvoicetemplate();
        $editedInvoicetemplate = $this->fakeInvoicetemplateData();

        $this->json('PUT', '/api/v1/invoicetemplates/'.$invoicetemplate->id, $editedInvoicetemplate);

        $this->assertApiResponse($editedInvoicetemplate);
    }

    /**
     * @test
     */
    public function testDeleteInvoicetemplate()
    {
        $invoicetemplate = $this->makeInvoicetemplate();
        $this->json('DELETE', '/api/v1/invoicetemplates/'.$invoicetemplate->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/invoicetemplates/'.$invoicetemplate->id);

        $this->assertResponseStatus(404);
    }
}
