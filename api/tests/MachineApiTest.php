<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MachineApiTest extends TestCase
{
    use MakeMachineTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateMachine()
    {
        $machine = $this->fakeMachineData();
        $this->json('POST', '/api/v1/machines', $machine);

        $this->assertApiResponse($machine);
    }

    /**
     * @test
     */
    public function testReadMachine()
    {
        $machine = $this->makeMachine();
        $this->json('GET', '/api/v1/machines/'.$machine->id);

        $this->assertApiResponse($machine->toArray());
    }

    /**
     * @test
     */
    public function testUpdateMachine()
    {
        $machine = $this->makeMachine();
        $editedMachine = $this->fakeMachineData();

        $this->json('PUT', '/api/v1/machines/'.$machine->id, $editedMachine);

        $this->assertApiResponse($editedMachine);
    }

    /**
     * @test
     */
    public function testDeleteMachine()
    {
        $machine = $this->makeMachine();
        $this->json('DELETE', '/api/v1/machines/'.$machine->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/machines/'.$machine->id);

        $this->assertResponseStatus(404);
    }
}
