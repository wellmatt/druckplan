<?php

use App\Models\MachineGroup;
use App\Repositories\MachineGroupRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MachineGroupRepositoryTest extends TestCase
{
    use MakeMachineGroupTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var MachineGroupRepository
     */
    protected $machineGroupRepo;

    public function setUp()
    {
        parent::setUp();
        $this->machineGroupRepo = App::make(MachineGroupRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateMachineGroup()
    {
        $machineGroup = $this->fakeMachineGroupData();
        $createdMachineGroup = $this->machineGroupRepo->create($machineGroup);
        $createdMachineGroup = $createdMachineGroup->toArray();
        $this->assertArrayHasKey('id', $createdMachineGroup);
        $this->assertNotNull($createdMachineGroup['id'], 'Created MachineGroup must have id specified');
        $this->assertNotNull(MachineGroup::find($createdMachineGroup['id']), 'MachineGroup with given id must be in DB');
        $this->assertModelData($machineGroup, $createdMachineGroup);
    }

    /**
     * @test read
     */
    public function testReadMachineGroup()
    {
        $machineGroup = $this->makeMachineGroup();
        $dbMachineGroup = $this->machineGroupRepo->find($machineGroup->id);
        $dbMachineGroup = $dbMachineGroup->toArray();
        $this->assertModelData($machineGroup->toArray(), $dbMachineGroup);
    }

    /**
     * @test update
     */
    public function testUpdateMachineGroup()
    {
        $machineGroup = $this->makeMachineGroup();
        $fakeMachineGroup = $this->fakeMachineGroupData();
        $updatedMachineGroup = $this->machineGroupRepo->update($fakeMachineGroup, $machineGroup->id);
        $this->assertModelData($fakeMachineGroup, $updatedMachineGroup->toArray());
        $dbMachineGroup = $this->machineGroupRepo->find($machineGroup->id);
        $this->assertModelData($fakeMachineGroup, $dbMachineGroup->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteMachineGroup()
    {
        $machineGroup = $this->makeMachineGroup();
        $resp = $this->machineGroupRepo->delete($machineGroup->id);
        $this->assertTrue($resp);
        $this->assertNull(MachineGroup::find($machineGroup->id), 'MachineGroup should not exist in DB');
    }
}
