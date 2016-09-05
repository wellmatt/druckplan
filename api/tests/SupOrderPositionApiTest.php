<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupOrderPositionApiTest extends TestCase
{
    use MakeSupOrderPositionTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSupOrderPosition()
    {
        $supOrderPosition = $this->fakeSupOrderPositionData();
        $this->json('POST', '/api/v1/supOrderPositions', $supOrderPosition);

        $this->assertApiResponse($supOrderPosition);
    }

    /**
     * @test
     */
    public function testReadSupOrderPosition()
    {
        $supOrderPosition = $this->makeSupOrderPosition();
        $this->json('GET', '/api/v1/supOrderPositions/'.$supOrderPosition->id);

        $this->assertApiResponse($supOrderPosition->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSupOrderPosition()
    {
        $supOrderPosition = $this->makeSupOrderPosition();
        $editedSupOrderPosition = $this->fakeSupOrderPositionData();

        $this->json('PUT', '/api/v1/supOrderPositions/'.$supOrderPosition->id, $editedSupOrderPosition);

        $this->assertApiResponse($editedSupOrderPosition);
    }

    /**
     * @test
     */
    public function testDeleteSupOrderPosition()
    {
        $supOrderPosition = $this->makeSupOrderPosition();
        $this->json('DELETE', '/api/v1/supOrderPositions/'.$supOrderPosition->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/supOrderPositions/'.$supOrderPosition->id);

        $this->assertResponseStatus(404);
    }
}
