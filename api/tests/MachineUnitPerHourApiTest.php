<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MachineUnitPerHourApiTest extends TestCase
{
    use MakeMachineUnitPerHourTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateMachineUnitPerHour()
    {
        $machineUnitPerHour = $this->fakeMachineUnitPerHourData();
        $this->json('POST', '/api/v1/machineUnitPerHours', $machineUnitPerHour);

        $this->assertApiResponse($machineUnitPerHour);
    }

    /**
     * @test
     */
    public function testReadMachineUnitPerHour()
    {
        $machineUnitPerHour = $this->makeMachineUnitPerHour();
        $this->json('GET', '/api/v1/machineUnitPerHours/'.$machineUnitPerHour->id);

        $this->assertApiResponse($machineUnitPerHour->toArray());
    }

    /**
     * @test
     */
    public function testUpdateMachineUnitPerHour()
    {
        $machineUnitPerHour = $this->makeMachineUnitPerHour();
        $editedMachineUnitPerHour = $this->fakeMachineUnitPerHourData();

        $this->json('PUT', '/api/v1/machineUnitPerHours/'.$machineUnitPerHour->id, $editedMachineUnitPerHour);

        $this->assertApiResponse($editedMachineUnitPerHour);
    }

    /**
     * @test
     */
    public function testDeleteMachineUnitPerHour()
    {
        $machineUnitPerHour = $this->makeMachineUnitPerHour();
        $this->json('DELETE', '/api/v1/machineUnitPerHours/'.$machineUnitPerHour->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/machineUnitPerHours/'.$machineUnitPerHour->id);

        $this->assertResponseStatus(404);
    }
}
