<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserEmailApiTest extends TestCase
{
    use MakeUserEmailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateUserEmail()
    {
        $userEmail = $this->fakeUserEmailData();
        $this->json('POST', '/api/v1/userEmails', $userEmail);

        $this->assertApiResponse($userEmail);
    }

    /**
     * @test
     */
    public function testReadUserEmail()
    {
        $userEmail = $this->makeUserEmail();
        $this->json('GET', '/api/v1/userEmails/'.$userEmail->id);

        $this->assertApiResponse($userEmail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateUserEmail()
    {
        $userEmail = $this->makeUserEmail();
        $editedUserEmail = $this->fakeUserEmailData();

        $this->json('PUT', '/api/v1/userEmails/'.$userEmail->id, $editedUserEmail);

        $this->assertApiResponse($editedUserEmail);
    }

    /**
     * @test
     */
    public function testDeleteUserEmail()
    {
        $userEmail = $this->makeUserEmail();
        $this->json('DELETE', '/api/v1/userEmails/'.$userEmail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/userEmails/'.$userEmail->id);

        $this->assertResponseStatus(404);
    }
}
