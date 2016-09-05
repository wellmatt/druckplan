<?php

use App\Models\Paper;
use App\Repositories\PaperRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaperRepositoryTest extends TestCase
{
    use MakePaperTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaperRepository
     */
    protected $paperRepo;

    public function setUp()
    {
        parent::setUp();
        $this->paperRepo = App::make(PaperRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePaper()
    {
        $paper = $this->fakePaperData();
        $createdPaper = $this->paperRepo->create($paper);
        $createdPaper = $createdPaper->toArray();
        $this->assertArrayHasKey('id', $createdPaper);
        $this->assertNotNull($createdPaper['id'], 'Created Paper must have id specified');
        $this->assertNotNull(Paper::find($createdPaper['id']), 'Paper with given id must be in DB');
        $this->assertModelData($paper, $createdPaper);
    }

    /**
     * @test read
     */
    public function testReadPaper()
    {
        $paper = $this->makePaper();
        $dbPaper = $this->paperRepo->find($paper->id);
        $dbPaper = $dbPaper->toArray();
        $this->assertModelData($paper->toArray(), $dbPaper);
    }

    /**
     * @test update
     */
    public function testUpdatePaper()
    {
        $paper = $this->makePaper();
        $fakePaper = $this->fakePaperData();
        $updatedPaper = $this->paperRepo->update($fakePaper, $paper->id);
        $this->assertModelData($fakePaper, $updatedPaper->toArray());
        $dbPaper = $this->paperRepo->find($paper->id);
        $this->assertModelData($fakePaper, $dbPaper->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePaper()
    {
        $paper = $this->makePaper();
        $resp = $this->paperRepo->delete($paper->id);
        $this->assertTrue($resp);
        $this->assertNull(Paper::find($paper->id), 'Paper should not exist in DB');
    }
}
