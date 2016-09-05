<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FTPDownloadApiTest extends TestCase
{
    use MakeFTPDownloadTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFTPDownload()
    {
        $fTPDownload = $this->fakeFTPDownloadData();
        $this->json('POST', '/api/v1/fTPDownloads', $fTPDownload);

        $this->assertApiResponse($fTPDownload);
    }

    /**
     * @test
     */
    public function testReadFTPDownload()
    {
        $fTPDownload = $this->makeFTPDownload();
        $this->json('GET', '/api/v1/fTPDownloads/'.$fTPDownload->id);

        $this->assertApiResponse($fTPDownload->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFTPDownload()
    {
        $fTPDownload = $this->makeFTPDownload();
        $editedFTPDownload = $this->fakeFTPDownloadData();

        $this->json('PUT', '/api/v1/fTPDownloads/'.$fTPDownload->id, $editedFTPDownload);

        $this->assertApiResponse($editedFTPDownload);
    }

    /**
     * @test
     */
    public function testDeleteFTPDownload()
    {
        $fTPDownload = $this->makeFTPDownload();
        $this->json('DELETE', '/api/v1/fTPDownloads/'.$fTPDownload->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/fTPDownloads/'.$fTPDownload->id);

        $this->assertResponseStatus(404);
    }
}
