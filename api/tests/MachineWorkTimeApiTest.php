<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MachineWorkTimeApiTest extends TestCase
{
    use MakeMachineWorkTimeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateMachineWorkTime()
    {
        $machineWorkTime = $this->fakeMachineWorkTimeData();
        $this->json('POST', '/api/v1/machineWorkTimes', $machineWorkTime);

        $this->assertApiResponse($machineWorkTime);
    }

    /**
     * @test
     */
    public function testReadMachineWorkTime()
    {
        $machineWorkTime = $this->makeMachineWorkTime();
        $this->json('GET', '/api/v1/machineWorkTimes/'.$machineWorkTime->id);

        $this->assertApiResponse($machineWorkTime->toArray());
    }

    /**
     * @test
     */
    public function testUpdateMachineWorkTime()
    {
        $machineWorkTime = $this->makeMachineWorkTime();
        $editedMachineWorkTime = $this->fakeMachineWorkTimeData();

        $this->json('PUT', '/api/v1/machineWorkTimes/'.$machineWorkTime->id, $editedMachineWorkTime);

        $this->assertApiResponse($editedMachineWorkTime);
    }

    /**
     * @test
     */
    public function testDeleteMachineWorkTime()
    {
        $machineWorkTime = $this->makeMachineWorkTime();
        $this->json('DELETE', '/api/v1/machineWorkTimes/'.$machineWorkTime->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/machineWorkTimes/'.$machineWorkTime->id);

        $this->assertResponseStatus(404);
    }
}
