<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InvoiceemissionApiTest extends TestCase
{
    use MakeInvoiceemissionTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateInvoiceemission()
    {
        $invoiceemission = $this->fakeInvoiceemissionData();
        $this->json('POST', '/api/v1/invoiceemissions', $invoiceemission);

        $this->assertApiResponse($invoiceemission);
    }

    /**
     * @test
     */
    public function testReadInvoiceemission()
    {
        $invoiceemission = $this->makeInvoiceemission();
        $this->json('GET', '/api/v1/invoiceemissions/'.$invoiceemission->id);

        $this->assertApiResponse($invoiceemission->toArray());
    }

    /**
     * @test
     */
    public function testUpdateInvoiceemission()
    {
        $invoiceemission = $this->makeInvoiceemission();
        $editedInvoiceemission = $this->fakeInvoiceemissionData();

        $this->json('PUT', '/api/v1/invoiceemissions/'.$invoiceemission->id, $editedInvoiceemission);

        $this->assertApiResponse($editedInvoiceemission);
    }

    /**
     * @test
     */
    public function testDeleteInvoiceemission()
    {
        $invoiceemission = $this->makeInvoiceemission();
        $this->json('DELETE', '/api/v1/invoiceemissions/'.$invoiceemission->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/invoiceemissions/'.$invoiceemission->id);

        $this->assertResponseStatus(404);
    }
}
