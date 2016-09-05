<?php

use App\Models\Chromaticity;
use App\Repositories\ChromaticityRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChromaticityRepositoryTest extends TestCase
{
    use MakeChromaticityTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ChromaticityRepository
     */
    protected $chromaticityRepo;

    public function setUp()
    {
        parent::setUp();
        $this->chromaticityRepo = App::make(ChromaticityRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateChromaticity()
    {
        $chromaticity = $this->fakeChromaticityData();
        $createdChromaticity = $this->chromaticityRepo->create($chromaticity);
        $createdChromaticity = $createdChromaticity->toArray();
        $this->assertArrayHasKey('id', $createdChromaticity);
        $this->assertNotNull($createdChromaticity['id'], 'Created Chromaticity must have id specified');
        $this->assertNotNull(Chromaticity::find($createdChromaticity['id']), 'Chromaticity with given id must be in DB');
        $this->assertModelData($chromaticity, $createdChromaticity);
    }

    /**
     * @test read
     */
    public function testReadChromaticity()
    {
        $chromaticity = $this->makeChromaticity();
        $dbChromaticity = $this->chromaticityRepo->find($chromaticity->id);
        $dbChromaticity = $dbChromaticity->toArray();
        $this->assertModelData($chromaticity->toArray(), $dbChromaticity);
    }

    /**
     * @test update
     */
    public function testUpdateChromaticity()
    {
        $chromaticity = $this->makeChromaticity();
        $fakeChromaticity = $this->fakeChromaticityData();
        $updatedChromaticity = $this->chromaticityRepo->update($fakeChromaticity, $chromaticity->id);
        $this->assertModelData($fakeChromaticity, $updatedChromaticity->toArray());
        $dbChromaticity = $this->chromaticityRepo->find($chromaticity->id);
        $this->assertModelData($fakeChromaticity, $dbChromaticity->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteChromaticity()
    {
        $chromaticity = $this->makeChromaticity();
        $resp = $this->chromaticityRepo->delete($chromaticity->id);
        $this->assertTrue($resp);
        $this->assertNull(Chromaticity::find($chromaticity->id), 'Chromaticity should not exist in DB');
    }
}
