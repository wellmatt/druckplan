<?php

use App\Models\MachineLock;
use App\Repositories\MachineLockRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MachineLockRepositoryTest extends TestCase
{
    use MakeMachineLockTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var MachineLockRepository
     */
    protected $machineLockRepo;

    public function setUp()
    {
        parent::setUp();
        $this->machineLockRepo = App::make(MachineLockRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateMachineLock()
    {
        $machineLock = $this->fakeMachineLockData();
        $createdMachineLock = $this->machineLockRepo->create($machineLock);
        $createdMachineLock = $createdMachineLock->toArray();
        $this->assertArrayHasKey('id', $createdMachineLock);
        $this->assertNotNull($createdMachineLock['id'], 'Created MachineLock must have id specified');
        $this->assertNotNull(MachineLock::find($createdMachineLock['id']), 'MachineLock with given id must be in DB');
        $this->assertModelData($machineLock, $createdMachineLock);
    }

    /**
     * @test read
     */
    public function testReadMachineLock()
    {
        $machineLock = $this->makeMachineLock();
        $dbMachineLock = $this->machineLockRepo->find($machineLock->id);
        $dbMachineLock = $dbMachineLock->toArray();
        $this->assertModelData($machineLock->toArray(), $dbMachineLock);
    }

    /**
     * @test update
     */
    public function testUpdateMachineLock()
    {
        $machineLock = $this->makeMachineLock();
        $fakeMachineLock = $this->fakeMachineLockData();
        $updatedMachineLock = $this->machineLockRepo->update($fakeMachineLock, $machineLock->id);
        $this->assertModelData($fakeMachineLock, $updatedMachineLock->toArray());
        $dbMachineLock = $this->machineLockRepo->find($machineLock->id);
        $this->assertModelData($fakeMachineLock, $dbMachineLock->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteMachineLock()
    {
        $machineLock = $this->makeMachineLock();
        $resp = $this->machineLockRepo->delete($machineLock->id);
        $this->assertTrue($resp);
        $this->assertNull(MachineLock::find($machineLock->id), 'MachineLock should not exist in DB');
    }
}
