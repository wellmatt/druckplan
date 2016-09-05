<?php

use App\Models\Foldtype;
use App\Repositories\FoldtypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FoldtypeRepositoryTest extends TestCase
{
    use MakeFoldtypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FoldtypeRepository
     */
    protected $foldtypeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->foldtypeRepo = App::make(FoldtypeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFoldtype()
    {
        $foldtype = $this->fakeFoldtypeData();
        $createdFoldtype = $this->foldtypeRepo->create($foldtype);
        $createdFoldtype = $createdFoldtype->toArray();
        $this->assertArrayHasKey('id', $createdFoldtype);
        $this->assertNotNull($createdFoldtype['id'], 'Created Foldtype must have id specified');
        $this->assertNotNull(Foldtype::find($createdFoldtype['id']), 'Foldtype with given id must be in DB');
        $this->assertModelData($foldtype, $createdFoldtype);
    }

    /**
     * @test read
     */
    public function testReadFoldtype()
    {
        $foldtype = $this->makeFoldtype();
        $dbFoldtype = $this->foldtypeRepo->find($foldtype->id);
        $dbFoldtype = $dbFoldtype->toArray();
        $this->assertModelData($foldtype->toArray(), $dbFoldtype);
    }

    /**
     * @test update
     */
    public function testUpdateFoldtype()
    {
        $foldtype = $this->makeFoldtype();
        $fakeFoldtype = $this->fakeFoldtypeData();
        $updatedFoldtype = $this->foldtypeRepo->update($fakeFoldtype, $foldtype->id);
        $this->assertModelData($fakeFoldtype, $updatedFoldtype->toArray());
        $dbFoldtype = $this->foldtypeRepo->find($foldtype->id);
        $this->assertModelData($fakeFoldtype, $dbFoldtype->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFoldtype()
    {
        $foldtype = $this->makeFoldtype();
        $resp = $this->foldtypeRepo->delete($foldtype->id);
        $this->assertTrue($resp);
        $this->assertNull(Foldtype::find($foldtype->id), 'Foldtype should not exist in DB');
    }
}
