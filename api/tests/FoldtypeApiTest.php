<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FoldtypeApiTest extends TestCase
{
    use MakeFoldtypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFoldtype()
    {
        $foldtype = $this->fakeFoldtypeData();
        $this->json('POST', '/api/v1/foldtypes', $foldtype);

        $this->assertApiResponse($foldtype);
    }

    /**
     * @test
     */
    public function testReadFoldtype()
    {
        $foldtype = $this->makeFoldtype();
        $this->json('GET', '/api/v1/foldtypes/'.$foldtype->id);

        $this->assertApiResponse($foldtype->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFoldtype()
    {
        $foldtype = $this->makeFoldtype();
        $editedFoldtype = $this->fakeFoldtypeData();

        $this->json('PUT', '/api/v1/foldtypes/'.$foldtype->id, $editedFoldtype);

        $this->assertApiResponse($editedFoldtype);
    }

    /**
     * @test
     */
    public function testDeleteFoldtype()
    {
        $foldtype = $this->makeFoldtype();
        $this->json('DELETE', '/api/v1/foldtypes/'.$foldtype->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/foldtypes/'.$foldtype->id);

        $this->assertResponseStatus(404);
    }
}
