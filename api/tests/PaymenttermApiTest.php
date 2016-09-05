<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaymenttermApiTest extends TestCase
{
    use MakePaymenttermTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePaymentterm()
    {
        $paymentterm = $this->fakePaymenttermData();
        $this->json('POST', '/api/v1/paymentterms', $paymentterm);

        $this->assertApiResponse($paymentterm);
    }

    /**
     * @test
     */
    public function testReadPaymentterm()
    {
        $paymentterm = $this->makePaymentterm();
        $this->json('GET', '/api/v1/paymentterms/'.$paymentterm->id);

        $this->assertApiResponse($paymentterm->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePaymentterm()
    {
        $paymentterm = $this->makePaymentterm();
        $editedPaymentterm = $this->fakePaymenttermData();

        $this->json('PUT', '/api/v1/paymentterms/'.$paymentterm->id, $editedPaymentterm);

        $this->assertApiResponse($editedPaymentterm);
    }

    /**
     * @test
     */
    public function testDeletePaymentterm()
    {
        $paymentterm = $this->makePaymentterm();
        $this->json('DELETE', '/api/v1/paymentterms/'.$paymentterm->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/paymentterms/'.$paymentterm->id);

        $this->assertResponseStatus(404);
    }
}
