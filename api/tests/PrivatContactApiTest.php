<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PrivatContactApiTest extends TestCase
{
    use MakePrivatContactTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePrivatContact()
    {
        $privatContact = $this->fakePrivatContactData();
        $this->json('POST', '/api/v1/privatContacts', $privatContact);

        $this->assertApiResponse($privatContact);
    }

    /**
     * @test
     */
    public function testReadPrivatContact()
    {
        $privatContact = $this->makePrivatContact();
        $this->json('GET', '/api/v1/privatContacts/'.$privatContact->id);

        $this->assertApiResponse($privatContact->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePrivatContact()
    {
        $privatContact = $this->makePrivatContact();
        $editedPrivatContact = $this->fakePrivatContactData();

        $this->json('PUT', '/api/v1/privatContacts/'.$privatContact->id, $editedPrivatContact);

        $this->assertApiResponse($editedPrivatContact);
    }

    /**
     * @test
     */
    public function testDeletePrivatContact()
    {
        $privatContact = $this->makePrivatContact();
        $this->json('DELETE', '/api/v1/privatContacts/'.$privatContact->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/privatContacts/'.$privatContact->id);

        $this->assertResponseStatus(404);
    }
}
