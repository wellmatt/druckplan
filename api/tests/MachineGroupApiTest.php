<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MachineGroupApiTest extends TestCase
{
    use MakeMachineGroupTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateMachineGroup()
    {
        $machineGroup = $this->fakeMachineGroupData();
        $this->json('POST', '/api/v1/machineGroups', $machineGroup);

        $this->assertApiResponse($machineGroup);
    }

    /**
     * @test
     */
    public function testReadMachineGroup()
    {
        $machineGroup = $this->makeMachineGroup();
        $this->json('GET', '/api/v1/machineGroups/'.$machineGroup->id);

        $this->assertApiResponse($machineGroup->toArray());
    }

    /**
     * @test
     */
    public function testUpdateMachineGroup()
    {
        $machineGroup = $this->makeMachineGroup();
        $editedMachineGroup = $this->fakeMachineGroupData();

        $this->json('PUT', '/api/v1/machineGroups/'.$machineGroup->id, $editedMachineGroup);

        $this->assertApiResponse($editedMachineGroup);
    }

    /**
     * @test
     */
    public function testDeleteMachineGroup()
    {
        $machineGroup = $this->makeMachineGroup();
        $this->json('DELETE', '/api/v1/machineGroups/'.$machineGroup->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/machineGroups/'.$machineGroup->id);

        $this->assertResponseStatus(404);
    }
}
