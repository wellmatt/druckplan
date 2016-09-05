<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FTPCustuploadApiTest extends TestCase
{
    use MakeFTPCustuploadTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFTPCustupload()
    {
        $fTPCustupload = $this->fakeFTPCustuploadData();
        $this->json('POST', '/api/v1/fTPCustuploads', $fTPCustupload);

        $this->assertApiResponse($fTPCustupload);
    }

    /**
     * @test
     */
    public function testReadFTPCustupload()
    {
        $fTPCustupload = $this->makeFTPCustupload();
        $this->json('GET', '/api/v1/fTPCustuploads/'.$fTPCustupload->id);

        $this->assertApiResponse($fTPCustupload->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFTPCustupload()
    {
        $fTPCustupload = $this->makeFTPCustupload();
        $editedFTPCustupload = $this->fakeFTPCustuploadData();

        $this->json('PUT', '/api/v1/fTPCustuploads/'.$fTPCustupload->id, $editedFTPCustupload);

        $this->assertApiResponse($editedFTPCustupload);
    }

    /**
     * @test
     */
    public function testDeleteFTPCustupload()
    {
        $fTPCustupload = $this->makeFTPCustupload();
        $this->json('DELETE', '/api/v1/fTPCustuploads/'.$fTPCustupload->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/fTPCustuploads/'.$fTPCustupload->id);

        $this->assertResponseStatus(404);
    }
}
