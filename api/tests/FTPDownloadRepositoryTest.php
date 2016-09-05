<?php

use App\Models\FTPDownload;
use App\Repositories\FTPDownloadRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FTPDownloadRepositoryTest extends TestCase
{
    use MakeFTPDownloadTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FTPDownloadRepository
     */
    protected $fTPDownloadRepo;

    public function setUp()
    {
        parent::setUp();
        $this->fTPDownloadRepo = App::make(FTPDownloadRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFTPDownload()
    {
        $fTPDownload = $this->fakeFTPDownloadData();
        $createdFTPDownload = $this->fTPDownloadRepo->create($fTPDownload);
        $createdFTPDownload = $createdFTPDownload->toArray();
        $this->assertArrayHasKey('id', $createdFTPDownload);
        $this->assertNotNull($createdFTPDownload['id'], 'Created FTPDownload must have id specified');
        $this->assertNotNull(FTPDownload::find($createdFTPDownload['id']), 'FTPDownload with given id must be in DB');
        $this->assertModelData($fTPDownload, $createdFTPDownload);
    }

    /**
     * @test read
     */
    public function testReadFTPDownload()
    {
        $fTPDownload = $this->makeFTPDownload();
        $dbFTPDownload = $this->fTPDownloadRepo->find($fTPDownload->id);
        $dbFTPDownload = $dbFTPDownload->toArray();
        $this->assertModelData($fTPDownload->toArray(), $dbFTPDownload);
    }

    /**
     * @test update
     */
    public function testUpdateFTPDownload()
    {
        $fTPDownload = $this->makeFTPDownload();
        $fakeFTPDownload = $this->fakeFTPDownloadData();
        $updatedFTPDownload = $this->fTPDownloadRepo->update($fakeFTPDownload, $fTPDownload->id);
        $this->assertModelData($fakeFTPDownload, $updatedFTPDownload->toArray());
        $dbFTPDownload = $this->fTPDownloadRepo->find($fTPDownload->id);
        $this->assertModelData($fakeFTPDownload, $dbFTPDownload->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFTPDownload()
    {
        $fTPDownload = $this->makeFTPDownload();
        $resp = $this->fTPDownloadRepo->delete($fTPDownload->id);
        $this->assertTrue($resp);
        $this->assertNull(FTPDownload::find($fTPDownload->id), 'FTPDownload should not exist in DB');
    }
}
