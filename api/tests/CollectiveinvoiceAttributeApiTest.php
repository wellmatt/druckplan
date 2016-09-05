<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CollectiveinvoiceAttributeApiTest extends TestCase
{
    use MakeCollectiveinvoiceAttributeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCollectiveinvoiceAttribute()
    {
        $collectiveinvoiceAttribute = $this->fakeCollectiveinvoiceAttributeData();
        $this->json('POST', '/api/v1/collectiveinvoiceAttributes', $collectiveinvoiceAttribute);

        $this->assertApiResponse($collectiveinvoiceAttribute);
    }

    /**
     * @test
     */
    public function testReadCollectiveinvoiceAttribute()
    {
        $collectiveinvoiceAttribute = $this->makeCollectiveinvoiceAttribute();
        $this->json('GET', '/api/v1/collectiveinvoiceAttributes/'.$collectiveinvoiceAttribute->id);

        $this->assertApiResponse($collectiveinvoiceAttribute->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCollectiveinvoiceAttribute()
    {
        $collectiveinvoiceAttribute = $this->makeCollectiveinvoiceAttribute();
        $editedCollectiveinvoiceAttribute = $this->fakeCollectiveinvoiceAttributeData();

        $this->json('PUT', '/api/v1/collectiveinvoiceAttributes/'.$collectiveinvoiceAttribute->id, $editedCollectiveinvoiceAttribute);

        $this->assertApiResponse($editedCollectiveinvoiceAttribute);
    }

    /**
     * @test
     */
    public function testDeleteCollectiveinvoiceAttribute()
    {
        $collectiveinvoiceAttribute = $this->makeCollectiveinvoiceAttribute();
        $this->json('DELETE', '/api/v1/collectiveinvoiceAttributes/'.$collectiveinvoiceAttribute->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/collectiveinvoiceAttributes/'.$collectiveinvoiceAttribute->id);

        $this->assertResponseStatus(404);
    }
}
