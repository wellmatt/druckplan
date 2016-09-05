<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BusinesscontactApiTest extends TestCase
{
    use MakeBusinesscontactTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBusinesscontact()
    {
        $businesscontact = $this->fakeBusinesscontactData();
        $this->json('POST', '/api/v1/businesscontacts', $businesscontact);

        $this->assertApiResponse($businesscontact);
    }

    /**
     * @test
     */
    public function testReadBusinesscontact()
    {
        $businesscontact = $this->makeBusinesscontact();
        $this->json('GET', '/api/v1/businesscontacts/'.$businesscontact->id);

        $this->assertApiResponse($businesscontact->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBusinesscontact()
    {
        $businesscontact = $this->makeBusinesscontact();
        $editedBusinesscontact = $this->fakeBusinesscontactData();

        $this->json('PUT', '/api/v1/businesscontacts/'.$businesscontact->id, $editedBusinesscontact);

        $this->assertApiResponse($editedBusinesscontact);
    }

    /**
     * @test
     */
    public function testDeleteBusinesscontact()
    {
        $businesscontact = $this->makeBusinesscontact();
        $this->json('DELETE', '/api/v1/businesscontacts/'.$businesscontact->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/businesscontacts/'.$businesscontact->id);

        $this->assertResponseStatus(404);
    }
}
