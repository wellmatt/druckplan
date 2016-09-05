<?php

use App\Models\PaperWeight;
use App\Repositories\PaperWeightRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaperWeightRepositoryTest extends TestCase
{
    use MakePaperWeightTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaperWeightRepository
     */
    protected $paperWeightRepo;

    public function setUp()
    {
        parent::setUp();
        $this->paperWeightRepo = App::make(PaperWeightRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePaperWeight()
    {
        $paperWeight = $this->fakePaperWeightData();
        $createdPaperWeight = $this->paperWeightRepo->create($paperWeight);
        $createdPaperWeight = $createdPaperWeight->toArray();
        $this->assertArrayHasKey('id', $createdPaperWeight);
        $this->assertNotNull($createdPaperWeight['id'], 'Created PaperWeight must have id specified');
        $this->assertNotNull(PaperWeight::find($createdPaperWeight['id']), 'PaperWeight with given id must be in DB');
        $this->assertModelData($paperWeight, $createdPaperWeight);
    }

    /**
     * @test read
     */
    public function testReadPaperWeight()
    {
        $paperWeight = $this->makePaperWeight();
        $dbPaperWeight = $this->paperWeightRepo->find($paperWeight->id);
        $dbPaperWeight = $dbPaperWeight->toArray();
        $this->assertModelData($paperWeight->toArray(), $dbPaperWeight);
    }

    /**
     * @test update
     */
    public function testUpdatePaperWeight()
    {
        $paperWeight = $this->makePaperWeight();
        $fakePaperWeight = $this->fakePaperWeightData();
        $updatedPaperWeight = $this->paperWeightRepo->update($fakePaperWeight, $paperWeight->id);
        $this->assertModelData($fakePaperWeight, $updatedPaperWeight->toArray());
        $dbPaperWeight = $this->paperWeightRepo->find($paperWeight->id);
        $this->assertModelData($fakePaperWeight, $dbPaperWeight->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePaperWeight()
    {
        $paperWeight = $this->makePaperWeight();
        $resp = $this->paperWeightRepo->delete($paperWeight->id);
        $this->assertTrue($resp);
        $this->assertNull(PaperWeight::find($paperWeight->id), 'PaperWeight should not exist in DB');
    }
}
