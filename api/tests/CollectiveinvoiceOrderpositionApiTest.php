<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CollectiveinvoiceOrderpositionApiTest extends TestCase
{
    use MakeCollectiveinvoiceOrderpositionTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCollectiveinvoiceOrderposition()
    {
        $collectiveinvoiceOrderposition = $this->fakeCollectiveinvoiceOrderpositionData();
        $this->json('POST', '/api/v1/collectiveinvoiceOrderpositions', $collectiveinvoiceOrderposition);

        $this->assertApiResponse($collectiveinvoiceOrderposition);
    }

    /**
     * @test
     */
    public function testReadCollectiveinvoiceOrderposition()
    {
        $collectiveinvoiceOrderposition = $this->makeCollectiveinvoiceOrderposition();
        $this->json('GET', '/api/v1/collectiveinvoiceOrderpositions/'.$collectiveinvoiceOrderposition->id);

        $this->assertApiResponse($collectiveinvoiceOrderposition->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCollectiveinvoiceOrderposition()
    {
        $collectiveinvoiceOrderposition = $this->makeCollectiveinvoiceOrderposition();
        $editedCollectiveinvoiceOrderposition = $this->fakeCollectiveinvoiceOrderpositionData();

        $this->json('PUT', '/api/v1/collectiveinvoiceOrderpositions/'.$collectiveinvoiceOrderposition->id, $editedCollectiveinvoiceOrderposition);

        $this->assertApiResponse($editedCollectiveinvoiceOrderposition);
    }

    /**
     * @test
     */
    public function testDeleteCollectiveinvoiceOrderposition()
    {
        $collectiveinvoiceOrderposition = $this->makeCollectiveinvoiceOrderposition();
        $this->json('DELETE', '/api/v1/collectiveinvoiceOrderpositions/'.$collectiveinvoiceOrderposition->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/collectiveinvoiceOrderpositions/'.$collectiveinvoiceOrderposition->id);

        $this->assertResponseStatus(404);
    }
}
