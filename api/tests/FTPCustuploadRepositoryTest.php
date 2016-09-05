<?php

use App\Models\FTPCustupload;
use App\Repositories\FTPCustuploadRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FTPCustuploadRepositoryTest extends TestCase
{
    use MakeFTPCustuploadTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FTPCustuploadRepository
     */
    protected $fTPCustuploadRepo;

    public function setUp()
    {
        parent::setUp();
        $this->fTPCustuploadRepo = App::make(FTPCustuploadRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFTPCustupload()
    {
        $fTPCustupload = $this->fakeFTPCustuploadData();
        $createdFTPCustupload = $this->fTPCustuploadRepo->create($fTPCustupload);
        $createdFTPCustupload = $createdFTPCustupload->toArray();
        $this->assertArrayHasKey('id', $createdFTPCustupload);
        $this->assertNotNull($createdFTPCustupload['id'], 'Created FTPCustupload must have id specified');
        $this->assertNotNull(FTPCustupload::find($createdFTPCustupload['id']), 'FTPCustupload with given id must be in DB');
        $this->assertModelData($fTPCustupload, $createdFTPCustupload);
    }

    /**
     * @test read
     */
    public function testReadFTPCustupload()
    {
        $fTPCustupload = $this->makeFTPCustupload();
        $dbFTPCustupload = $this->fTPCustuploadRepo->find($fTPCustupload->id);
        $dbFTPCustupload = $dbFTPCustupload->toArray();
        $this->assertModelData($fTPCustupload->toArray(), $dbFTPCustupload);
    }

    /**
     * @test update
     */
    public function testUpdateFTPCustupload()
    {
        $fTPCustupload = $this->makeFTPCustupload();
        $fakeFTPCustupload = $this->fakeFTPCustuploadData();
        $updatedFTPCustupload = $this->fTPCustuploadRepo->update($fakeFTPCustupload, $fTPCustupload->id);
        $this->assertModelData($fakeFTPCustupload, $updatedFTPCustupload->toArray());
        $dbFTPCustupload = $this->fTPCustuploadRepo->find($fTPCustupload->id);
        $this->assertModelData($fakeFTPCustupload, $dbFTPCustupload->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFTPCustupload()
    {
        $fTPCustupload = $this->makeFTPCustupload();
        $resp = $this->fTPCustuploadRepo->delete($fTPCustupload->id);
        $this->assertTrue($resp);
        $this->assertNull(FTPCustupload::find($fTPCustupload->id), 'FTPCustupload should not exist in DB');
    }
}
