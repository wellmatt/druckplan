<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CollectiveinvoiceApiTest extends TestCase
{
    use MakeCollectiveinvoiceTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCollectiveinvoice()
    {
        $collectiveinvoice = $this->fakeCollectiveinvoiceData();
        $this->json('POST', '/api/v1/collectiveinvoices', $collectiveinvoice);

        $this->assertApiResponse($collectiveinvoice);
    }

    /**
     * @test
     */
    public function testReadCollectiveinvoice()
    {
        $collectiveinvoice = $this->makeCollectiveinvoice();
        $this->json('GET', '/api/v1/collectiveinvoices/'.$collectiveinvoice->id);

        $this->assertApiResponse($collectiveinvoice->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCollectiveinvoice()
    {
        $collectiveinvoice = $this->makeCollectiveinvoice();
        $editedCollectiveinvoice = $this->fakeCollectiveinvoiceData();

        $this->json('PUT', '/api/v1/collectiveinvoices/'.$collectiveinvoice->id, $editedCollectiveinvoice);

        $this->assertApiResponse($editedCollectiveinvoice);
    }

    /**
     * @test
     */
    public function testDeleteCollectiveinvoice()
    {
        $collectiveinvoice = $this->makeCollectiveinvoice();
        $this->json('DELETE', '/api/v1/collectiveinvoices/'.$collectiveinvoice->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/collectiveinvoices/'.$collectiveinvoice->id);

        $this->assertResponseStatus(404);
    }
}
