<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserGroupApiTest extends TestCase
{
    use MakeUserGroupTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateUserGroup()
    {
        $userGroup = $this->fakeUserGroupData();
        $this->json('POST', '/api/v1/userGroups', $userGroup);

        $this->assertApiResponse($userGroup);
    }

    /**
     * @test
     */
    public function testReadUserGroup()
    {
        $userGroup = $this->makeUserGroup();
        $this->json('GET', '/api/v1/userGroups/'.$userGroup->id);

        $this->assertApiResponse($userGroup->toArray());
    }

    /**
     * @test
     */
    public function testUpdateUserGroup()
    {
        $userGroup = $this->makeUserGroup();
        $editedUserGroup = $this->fakeUserGroupData();

        $this->json('PUT', '/api/v1/userGroups/'.$userGroup->id, $editedUserGroup);

        $this->assertApiResponse($editedUserGroup);
    }

    /**
     * @test
     */
    public function testDeleteUserGroup()
    {
        $userGroup = $this->makeUserGroup();
        $this->json('DELETE', '/api/v1/userGroups/'.$userGroup->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/userGroups/'.$userGroup->id);

        $this->assertResponseStatus(404);
    }
}
