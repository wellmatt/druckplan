<?php

use App\Models\MachineUnitPerHour;
use App\Repositories\MachineUnitPerHourRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MachineUnitPerHourRepositoryTest extends TestCase
{
    use MakeMachineUnitPerHourTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var MachineUnitPerHourRepository
     */
    protected $machineUnitPerHourRepo;

    public function setUp()
    {
        parent::setUp();
        $this->machineUnitPerHourRepo = App::make(MachineUnitPerHourRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateMachineUnitPerHour()
    {
        $machineUnitPerHour = $this->fakeMachineUnitPerHourData();
        $createdMachineUnitPerHour = $this->machineUnitPerHourRepo->create($machineUnitPerHour);
        $createdMachineUnitPerHour = $createdMachineUnitPerHour->toArray();
        $this->assertArrayHasKey('id', $createdMachineUnitPerHour);
        $this->assertNotNull($createdMachineUnitPerHour['id'], 'Created MachineUnitPerHour must have id specified');
        $this->assertNotNull(MachineUnitPerHour::find($createdMachineUnitPerHour['id']), 'MachineUnitPerHour with given id must be in DB');
        $this->assertModelData($machineUnitPerHour, $createdMachineUnitPerHour);
    }

    /**
     * @test read
     */
    public function testReadMachineUnitPerHour()
    {
        $machineUnitPerHour = $this->makeMachineUnitPerHour();
        $dbMachineUnitPerHour = $this->machineUnitPerHourRepo->find($machineUnitPerHour->id);
        $dbMachineUnitPerHour = $dbMachineUnitPerHour->toArray();
        $this->assertModelData($machineUnitPerHour->toArray(), $dbMachineUnitPerHour);
    }

    /**
     * @test update
     */
    public function testUpdateMachineUnitPerHour()
    {
        $machineUnitPerHour = $this->makeMachineUnitPerHour();
        $fakeMachineUnitPerHour = $this->fakeMachineUnitPerHourData();
        $updatedMachineUnitPerHour = $this->machineUnitPerHourRepo->update($fakeMachineUnitPerHour, $machineUnitPerHour->id);
        $this->assertModelData($fakeMachineUnitPerHour, $updatedMachineUnitPerHour->toArray());
        $dbMachineUnitPerHour = $this->machineUnitPerHourRepo->find($machineUnitPerHour->id);
        $this->assertModelData($fakeMachineUnitPerHour, $dbMachineUnitPerHour->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteMachineUnitPerHour()
    {
        $machineUnitPerHour = $this->makeMachineUnitPerHour();
        $resp = $this->machineUnitPerHourRepo->delete($machineUnitPerHour->id);
        $this->assertTrue($resp);
        $this->assertNull(MachineUnitPerHour::find($machineUnitPerHour->id), 'MachineUnitPerHour should not exist in DB');
    }
}
