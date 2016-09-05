<?php

use App\Models\ProductFormat;
use App\Repositories\ProductFormatRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductFormatRepositoryTest extends TestCase
{
    use MakeProductFormatTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ProductFormatRepository
     */
    protected $productFormatRepo;

    public function setUp()
    {
        parent::setUp();
        $this->productFormatRepo = App::make(ProductFormatRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateProductFormat()
    {
        $productFormat = $this->fakeProductFormatData();
        $createdProductFormat = $this->productFormatRepo->create($productFormat);
        $createdProductFormat = $createdProductFormat->toArray();
        $this->assertArrayHasKey('id', $createdProductFormat);
        $this->assertNotNull($createdProductFormat['id'], 'Created ProductFormat must have id specified');
        $this->assertNotNull(ProductFormat::find($createdProductFormat['id']), 'ProductFormat with given id must be in DB');
        $this->assertModelData($productFormat, $createdProductFormat);
    }

    /**
     * @test read
     */
    public function testReadProductFormat()
    {
        $productFormat = $this->makeProductFormat();
        $dbProductFormat = $this->productFormatRepo->find($productFormat->id);
        $dbProductFormat = $dbProductFormat->toArray();
        $this->assertModelData($productFormat->toArray(), $dbProductFormat);
    }

    /**
     * @test update
     */
    public function testUpdateProductFormat()
    {
        $productFormat = $this->makeProductFormat();
        $fakeProductFormat = $this->fakeProductFormatData();
        $updatedProductFormat = $this->productFormatRepo->update($fakeProductFormat, $productFormat->id);
        $this->assertModelData($fakeProductFormat, $updatedProductFormat->toArray());
        $dbProductFormat = $this->productFormatRepo->find($productFormat->id);
        $this->assertModelData($fakeProductFormat, $dbProductFormat->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteProductFormat()
    {
        $productFormat = $this->makeProductFormat();
        $resp = $this->productFormatRepo->delete($productFormat->id);
        $this->assertTrue($resp);
        $this->assertNull(ProductFormat::find($productFormat->id), 'ProductFormat should not exist in DB');
    }
}
