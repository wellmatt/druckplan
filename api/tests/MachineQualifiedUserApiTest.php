<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MachineQualifiedUserApiTest extends TestCase
{
    use MakeMachineQualifiedUserTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateMachineQualifiedUser()
    {
        $machineQualifiedUser = $this->fakeMachineQualifiedUserData();
        $this->json('POST', '/api/v1/machineQualifiedUsers', $machineQualifiedUser);

        $this->assertApiResponse($machineQualifiedUser);
    }

    /**
     * @test
     */
    public function testReadMachineQualifiedUser()
    {
        $machineQualifiedUser = $this->makeMachineQualifiedUser();
        $this->json('GET', '/api/v1/machineQualifiedUsers/'.$machineQualifiedUser->id);

        $this->assertApiResponse($machineQualifiedUser->toArray());
    }

    /**
     * @test
     */
    public function testUpdateMachineQualifiedUser()
    {
        $machineQualifiedUser = $this->makeMachineQualifiedUser();
        $editedMachineQualifiedUser = $this->fakeMachineQualifiedUserData();

        $this->json('PUT', '/api/v1/machineQualifiedUsers/'.$machineQualifiedUser->id, $editedMachineQualifiedUser);

        $this->assertApiResponse($editedMachineQualifiedUser);
    }

    /**
     * @test
     */
    public function testDeleteMachineQualifiedUser()
    {
        $machineQualifiedUser = $this->makeMachineQualifiedUser();
        $this->json('DELETE', '/api/v1/machineQualifiedUsers/'.$machineQualifiedUser->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/machineQualifiedUsers/'.$machineQualifiedUser->id);

        $this->assertResponseStatus(404);
    }
}
