<?php

use App\Models\PaperSize;
use App\Repositories\PaperSizeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaperSizeRepositoryTest extends TestCase
{
    use MakePaperSizeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaperSizeRepository
     */
    protected $paperSizeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->paperSizeRepo = App::make(PaperSizeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePaperSize()
    {
        $paperSize = $this->fakePaperSizeData();
        $createdPaperSize = $this->paperSizeRepo->create($paperSize);
        $createdPaperSize = $createdPaperSize->toArray();
        $this->assertArrayHasKey('id', $createdPaperSize);
        $this->assertNotNull($createdPaperSize['id'], 'Created PaperSize must have id specified');
        $this->assertNotNull(PaperSize::find($createdPaperSize['id']), 'PaperSize with given id must be in DB');
        $this->assertModelData($paperSize, $createdPaperSize);
    }

    /**
     * @test read
     */
    public function testReadPaperSize()
    {
        $paperSize = $this->makePaperSize();
        $dbPaperSize = $this->paperSizeRepo->find($paperSize->id);
        $dbPaperSize = $dbPaperSize->toArray();
        $this->assertModelData($paperSize->toArray(), $dbPaperSize);
    }

    /**
     * @test update
     */
    public function testUpdatePaperSize()
    {
        $paperSize = $this->makePaperSize();
        $fakePaperSize = $this->fakePaperSizeData();
        $updatedPaperSize = $this->paperSizeRepo->update($fakePaperSize, $paperSize->id);
        $this->assertModelData($fakePaperSize, $updatedPaperSize->toArray());
        $dbPaperSize = $this->paperSizeRepo->find($paperSize->id);
        $this->assertModelData($fakePaperSize, $dbPaperSize->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePaperSize()
    {
        $paperSize = $this->makePaperSize();
        $resp = $this->paperSizeRepo->delete($paperSize->id);
        $this->assertTrue($resp);
        $this->assertNull(PaperSize::find($paperSize->id), 'PaperSize should not exist in DB');
    }
}
