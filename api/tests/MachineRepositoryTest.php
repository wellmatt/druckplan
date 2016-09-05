<?php

use App\Models\Machine;
use App\Repositories\MachineRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MachineRepositoryTest extends TestCase
{
    use MakeMachineTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var MachineRepository
     */
    protected $machineRepo;

    public function setUp()
    {
        parent::setUp();
        $this->machineRepo = App::make(MachineRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateMachine()
    {
        $machine = $this->fakeMachineData();
        $createdMachine = $this->machineRepo->create($machine);
        $createdMachine = $createdMachine->toArray();
        $this->assertArrayHasKey('id', $createdMachine);
        $this->assertNotNull($createdMachine['id'], 'Created Machine must have id specified');
        $this->assertNotNull(Machine::find($createdMachine['id']), 'Machine with given id must be in DB');
        $this->assertModelData($machine, $createdMachine);
    }

    /**
     * @test read
     */
    public function testReadMachine()
    {
        $machine = $this->makeMachine();
        $dbMachine = $this->machineRepo->find($machine->id);
        $dbMachine = $dbMachine->toArray();
        $this->assertModelData($machine->toArray(), $dbMachine);
    }

    /**
     * @test update
     */
    public function testUpdateMachine()
    {
        $machine = $this->makeMachine();
        $fakeMachine = $this->fakeMachineData();
        $updatedMachine = $this->machineRepo->update($fakeMachine, $machine->id);
        $this->assertModelData($fakeMachine, $updatedMachine->toArray());
        $dbMachine = $this->machineRepo->find($machine->id);
        $this->assertModelData($fakeMachine, $dbMachine->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteMachine()
    {
        $machine = $this->makeMachine();
        $resp = $this->machineRepo->delete($machine->id);
        $this->assertTrue($resp);
        $this->assertNull(Machine::find($machine->id), 'Machine should not exist in DB');
    }
}
