<?php

use App\Models\MachineWorkTime;
use App\Repositories\MachineWorkTimeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MachineWorkTimeRepositoryTest extends TestCase
{
    use MakeMachineWorkTimeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var MachineWorkTimeRepository
     */
    protected $machineWorkTimeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->machineWorkTimeRepo = App::make(MachineWorkTimeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateMachineWorkTime()
    {
        $machineWorkTime = $this->fakeMachineWorkTimeData();
        $createdMachineWorkTime = $this->machineWorkTimeRepo->create($machineWorkTime);
        $createdMachineWorkTime = $createdMachineWorkTime->toArray();
        $this->assertArrayHasKey('id', $createdMachineWorkTime);
        $this->assertNotNull($createdMachineWorkTime['id'], 'Created MachineWorkTime must have id specified');
        $this->assertNotNull(MachineWorkTime::find($createdMachineWorkTime['id']), 'MachineWorkTime with given id must be in DB');
        $this->assertModelData($machineWorkTime, $createdMachineWorkTime);
    }

    /**
     * @test read
     */
    public function testReadMachineWorkTime()
    {
        $machineWorkTime = $this->makeMachineWorkTime();
        $dbMachineWorkTime = $this->machineWorkTimeRepo->find($machineWorkTime->id);
        $dbMachineWorkTime = $dbMachineWorkTime->toArray();
        $this->assertModelData($machineWorkTime->toArray(), $dbMachineWorkTime);
    }

    /**
     * @test update
     */
    public function testUpdateMachineWorkTime()
    {
        $machineWorkTime = $this->makeMachineWorkTime();
        $fakeMachineWorkTime = $this->fakeMachineWorkTimeData();
        $updatedMachineWorkTime = $this->machineWorkTimeRepo->update($fakeMachineWorkTime, $machineWorkTime->id);
        $this->assertModelData($fakeMachineWorkTime, $updatedMachineWorkTime->toArray());
        $dbMachineWorkTime = $this->machineWorkTimeRepo->find($machineWorkTime->id);
        $this->assertModelData($fakeMachineWorkTime, $dbMachineWorkTime->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteMachineWorkTime()
    {
        $machineWorkTime = $this->makeMachineWorkTime();
        $resp = $this->machineWorkTimeRepo->delete($machineWorkTime->id);
        $this->assertTrue($resp);
        $this->assertNull(MachineWorkTime::find($machineWorkTime->id), 'MachineWorkTime should not exist in DB');
    }
}
