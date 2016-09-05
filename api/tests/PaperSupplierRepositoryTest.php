<?php

use App\Models\PaperSupplier;
use App\Repositories\PaperSupplierRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaperSupplierRepositoryTest extends TestCase
{
    use MakePaperSupplierTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaperSupplierRepository
     */
    protected $paperSupplierRepo;

    public function setUp()
    {
        parent::setUp();
        $this->paperSupplierRepo = App::make(PaperSupplierRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePaperSupplier()
    {
        $paperSupplier = $this->fakePaperSupplierData();
        $createdPaperSupplier = $this->paperSupplierRepo->create($paperSupplier);
        $createdPaperSupplier = $createdPaperSupplier->toArray();
        $this->assertArrayHasKey('id', $createdPaperSupplier);
        $this->assertNotNull($createdPaperSupplier['id'], 'Created PaperSupplier must have id specified');
        $this->assertNotNull(PaperSupplier::find($createdPaperSupplier['id']), 'PaperSupplier with given id must be in DB');
        $this->assertModelData($paperSupplier, $createdPaperSupplier);
    }

    /**
     * @test read
     */
    public function testReadPaperSupplier()
    {
        $paperSupplier = $this->makePaperSupplier();
        $dbPaperSupplier = $this->paperSupplierRepo->find($paperSupplier->id);
        $dbPaperSupplier = $dbPaperSupplier->toArray();
        $this->assertModelData($paperSupplier->toArray(), $dbPaperSupplier);
    }

    /**
     * @test update
     */
    public function testUpdatePaperSupplier()
    {
        $paperSupplier = $this->makePaperSupplier();
        $fakePaperSupplier = $this->fakePaperSupplierData();
        $updatedPaperSupplier = $this->paperSupplierRepo->update($fakePaperSupplier, $paperSupplier->id);
        $this->assertModelData($fakePaperSupplier, $updatedPaperSupplier->toArray());
        $dbPaperSupplier = $this->paperSupplierRepo->find($paperSupplier->id);
        $this->assertModelData($fakePaperSupplier, $dbPaperSupplier->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePaperSupplier()
    {
        $paperSupplier = $this->makePaperSupplier();
        $resp = $this->paperSupplierRepo->delete($paperSupplier->id);
        $this->assertTrue($resp);
        $this->assertNull(PaperSupplier::find($paperSupplier->id), 'PaperSupplier should not exist in DB');
    }
}
