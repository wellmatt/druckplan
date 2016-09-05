<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContactpersonApiTest extends TestCase
{
    use MakeContactpersonTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateContactperson()
    {
        $contactperson = $this->fakeContactpersonData();
        $this->json('POST', '/api/v1/contactpeople', $contactperson);

        $this->assertApiResponse($contactperson);
    }

    /**
     * @test
     */
    public function testReadContactperson()
    {
        $contactperson = $this->makeContactperson();
        $this->json('GET', '/api/v1/contactpeople/'.$contactperson->id);

        $this->assertApiResponse($contactperson->toArray());
    }

    /**
     * @test
     */
    public function testUpdateContactperson()
    {
        $contactperson = $this->makeContactperson();
        $editedContactperson = $this->fakeContactpersonData();

        $this->json('PUT', '/api/v1/contactpeople/'.$contactperson->id, $editedContactperson);

        $this->assertApiResponse($editedContactperson);
    }

    /**
     * @test
     */
    public function testDeleteContactperson()
    {
        $contactperson = $this->makeContactperson();
        $this->json('DELETE', '/api/v1/contactpeople/'.$contactperson->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/contactpeople/'.$contactperson->id);

        $this->assertResponseStatus(404);
    }
}
