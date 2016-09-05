<?php

use App\Models\MachineChromaticity;
use App\Repositories\MachineChromaticityRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MachineChromaticityRepositoryTest extends TestCase
{
    use MakeMachineChromaticityTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var MachineChromaticityRepository
     */
    protected $machineChromaticityRepo;

    public function setUp()
    {
        parent::setUp();
        $this->machineChromaticityRepo = App::make(MachineChromaticityRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateMachineChromaticity()
    {
        $machineChromaticity = $this->fakeMachineChromaticityData();
        $createdMachineChromaticity = $this->machineChromaticityRepo->create($machineChromaticity);
        $createdMachineChromaticity = $createdMachineChromaticity->toArray();
        $this->assertArrayHasKey('id', $createdMachineChromaticity);
        $this->assertNotNull($createdMachineChromaticity['id'], 'Created MachineChromaticity must have id specified');
        $this->assertNotNull(MachineChromaticity::find($createdMachineChromaticity['id']), 'MachineChromaticity with given id must be in DB');
        $this->assertModelData($machineChromaticity, $createdMachineChromaticity);
    }

    /**
     * @test read
     */
    public function testReadMachineChromaticity()
    {
        $machineChromaticity = $this->makeMachineChromaticity();
        $dbMachineChromaticity = $this->machineChromaticityRepo->find($machineChromaticity->id);
        $dbMachineChromaticity = $dbMachineChromaticity->toArray();
        $this->assertModelData($machineChromaticity->toArray(), $dbMachineChromaticity);
    }

    /**
     * @test update
     */
    public function testUpdateMachineChromaticity()
    {
        $machineChromaticity = $this->makeMachineChromaticity();
        $fakeMachineChromaticity = $this->fakeMachineChromaticityData();
        $updatedMachineChromaticity = $this->machineChromaticityRepo->update($fakeMachineChromaticity, $machineChromaticity->id);
        $this->assertModelData($fakeMachineChromaticity, $updatedMachineChromaticity->toArray());
        $dbMachineChromaticity = $this->machineChromaticityRepo->find($machineChromaticity->id);
        $this->assertModelData($fakeMachineChromaticity, $dbMachineChromaticity->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteMachineChromaticity()
    {
        $machineChromaticity = $this->makeMachineChromaticity();
        $resp = $this->machineChromaticityRepo->delete($machineChromaticity->id);
        $this->assertTrue($resp);
        $this->assertNull(MachineChromaticity::find($machineChromaticity->id), 'MachineChromaticity should not exist in DB');
    }
}
