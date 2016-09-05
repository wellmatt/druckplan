<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FinishingApiTest extends TestCase
{
    use MakeFinishingTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFinishing()
    {
        $finishing = $this->fakeFinishingData();
        $this->json('POST', '/api/v1/finishings', $finishing);

        $this->assertApiResponse($finishing);
    }

    /**
     * @test
     */
    public function testReadFinishing()
    {
        $finishing = $this->makeFinishing();
        $this->json('GET', '/api/v1/finishings/'.$finishing->id);

        $this->assertApiResponse($finishing->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFinishing()
    {
        $finishing = $this->makeFinishing();
        $editedFinishing = $this->fakeFinishingData();

        $this->json('PUT', '/api/v1/finishings/'.$finishing->id, $editedFinishing);

        $this->assertApiResponse($editedFinishing);
    }

    /**
     * @test
     */
    public function testDeleteFinishing()
    {
        $finishing = $this->makeFinishing();
        $this->json('DELETE', '/api/v1/finishings/'.$finishing->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/finishings/'.$finishing->id);

        $this->assertResponseStatus(404);
    }
}
