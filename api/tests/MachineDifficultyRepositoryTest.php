<?php

use App\Models\MachineDifficulty;
use App\Repositories\MachineDifficultyRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MachineDifficultyRepositoryTest extends TestCase
{
    use MakeMachineDifficultyTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var MachineDifficultyRepository
     */
    protected $machineDifficultyRepo;

    public function setUp()
    {
        parent::setUp();
        $this->machineDifficultyRepo = App::make(MachineDifficultyRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateMachineDifficulty()
    {
        $machineDifficulty = $this->fakeMachineDifficultyData();
        $createdMachineDifficulty = $this->machineDifficultyRepo->create($machineDifficulty);
        $createdMachineDifficulty = $createdMachineDifficulty->toArray();
        $this->assertArrayHasKey('id', $createdMachineDifficulty);
        $this->assertNotNull($createdMachineDifficulty['id'], 'Created MachineDifficulty must have id specified');
        $this->assertNotNull(MachineDifficulty::find($createdMachineDifficulty['id']), 'MachineDifficulty with given id must be in DB');
        $this->assertModelData($machineDifficulty, $createdMachineDifficulty);
    }

    /**
     * @test read
     */
    public function testReadMachineDifficulty()
    {
        $machineDifficulty = $this->makeMachineDifficulty();
        $dbMachineDifficulty = $this->machineDifficultyRepo->find($machineDifficulty->id);
        $dbMachineDifficulty = $dbMachineDifficulty->toArray();
        $this->assertModelData($machineDifficulty->toArray(), $dbMachineDifficulty);
    }

    /**
     * @test update
     */
    public function testUpdateMachineDifficulty()
    {
        $machineDifficulty = $this->makeMachineDifficulty();
        $fakeMachineDifficulty = $this->fakeMachineDifficultyData();
        $updatedMachineDifficulty = $this->machineDifficultyRepo->update($fakeMachineDifficulty, $machineDifficulty->id);
        $this->assertModelData($fakeMachineDifficulty, $updatedMachineDifficulty->toArray());
        $dbMachineDifficulty = $this->machineDifficultyRepo->find($machineDifficulty->id);
        $this->assertModelData($fakeMachineDifficulty, $dbMachineDifficulty->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteMachineDifficulty()
    {
        $machineDifficulty = $this->makeMachineDifficulty();
        $resp = $this->machineDifficultyRepo->delete($machineDifficulty->id);
        $this->assertTrue($resp);
        $this->assertNull(MachineDifficulty::find($machineDifficulty->id), 'MachineDifficulty should not exist in DB');
    }
}
