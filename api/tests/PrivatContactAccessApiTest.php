<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PrivatContactAccessApiTest extends TestCase
{
    use MakePrivatContactAccessTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePrivatContactAccess()
    {
        $privatContactAccess = $this->fakePrivatContactAccessData();
        $this->json('POST', '/api/v1/privatContactAccesses', $privatContactAccess);

        $this->assertApiResponse($privatContactAccess);
    }

    /**
     * @test
     */
    public function testReadPrivatContactAccess()
    {
        $privatContactAccess = $this->makePrivatContactAccess();
        $this->json('GET', '/api/v1/privatContactAccesses/'.$privatContactAccess->id);

        $this->assertApiResponse($privatContactAccess->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePrivatContactAccess()
    {
        $privatContactAccess = $this->makePrivatContactAccess();
        $editedPrivatContactAccess = $this->fakePrivatContactAccessData();

        $this->json('PUT', '/api/v1/privatContactAccesses/'.$privatContactAccess->id, $editedPrivatContactAccess);

        $this->assertApiResponse($editedPrivatContactAccess);
    }

    /**
     * @test
     */
    public function testDeletePrivatContactAccess()
    {
        $privatContactAccess = $this->makePrivatContactAccess();
        $this->json('DELETE', '/api/v1/privatContactAccesses/'.$privatContactAccess->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/privatContactAccesses/'.$privatContactAccess->id);

        $this->assertResponseStatus(404);
    }
}
