<?php

use App\Models\MachineQualifiedUser;
use App\Repositories\MachineQualifiedUserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MachineQualifiedUserRepositoryTest extends TestCase
{
    use MakeMachineQualifiedUserTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var MachineQualifiedUserRepository
     */
    protected $machineQualifiedUserRepo;

    public function setUp()
    {
        parent::setUp();
        $this->machineQualifiedUserRepo = App::make(MachineQualifiedUserRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateMachineQualifiedUser()
    {
        $machineQualifiedUser = $this->fakeMachineQualifiedUserData();
        $createdMachineQualifiedUser = $this->machineQualifiedUserRepo->create($machineQualifiedUser);
        $createdMachineQualifiedUser = $createdMachineQualifiedUser->toArray();
        $this->assertArrayHasKey('id', $createdMachineQualifiedUser);
        $this->assertNotNull($createdMachineQualifiedUser['id'], 'Created MachineQualifiedUser must have id specified');
        $this->assertNotNull(MachineQualifiedUser::find($createdMachineQualifiedUser['id']), 'MachineQualifiedUser with given id must be in DB');
        $this->assertModelData($machineQualifiedUser, $createdMachineQualifiedUser);
    }

    /**
     * @test read
     */
    public function testReadMachineQualifiedUser()
    {
        $machineQualifiedUser = $this->makeMachineQualifiedUser();
        $dbMachineQualifiedUser = $this->machineQualifiedUserRepo->find($machineQualifiedUser->id);
        $dbMachineQualifiedUser = $dbMachineQualifiedUser->toArray();
        $this->assertModelData($machineQualifiedUser->toArray(), $dbMachineQualifiedUser);
    }

    /**
     * @test update
     */
    public function testUpdateMachineQualifiedUser()
    {
        $machineQualifiedUser = $this->makeMachineQualifiedUser();
        $fakeMachineQualifiedUser = $this->fakeMachineQualifiedUserData();
        $updatedMachineQualifiedUser = $this->machineQualifiedUserRepo->update($fakeMachineQualifiedUser, $machineQualifiedUser->id);
        $this->assertModelData($fakeMachineQualifiedUser, $updatedMachineQualifiedUser->toArray());
        $dbMachineQualifiedUser = $this->machineQualifiedUserRepo->find($machineQualifiedUser->id);
        $this->assertModelData($fakeMachineQualifiedUser, $dbMachineQualifiedUser->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteMachineQualifiedUser()
    {
        $machineQualifiedUser = $this->makeMachineQualifiedUser();
        $resp = $this->machineQualifiedUserRepo->delete($machineQualifiedUser->id);
        $this->assertTrue($resp);
        $this->assertNull(MachineQualifiedUser::find($machineQualifiedUser->id), 'MachineQualifiedUser should not exist in DB');
    }
}
