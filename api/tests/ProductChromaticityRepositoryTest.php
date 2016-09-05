<?php

use App\Models\ProductChromaticity;
use App\Repositories\ProductChromaticityRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductChromaticityRepositoryTest extends TestCase
{
    use MakeProductChromaticityTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ProductChromaticityRepository
     */
    protected $productChromaticityRepo;

    public function setUp()
    {
        parent::setUp();
        $this->productChromaticityRepo = App::make(ProductChromaticityRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateProductChromaticity()
    {
        $productChromaticity = $this->fakeProductChromaticityData();
        $createdProductChromaticity = $this->productChromaticityRepo->create($productChromaticity);
        $createdProductChromaticity = $createdProductChromaticity->toArray();
        $this->assertArrayHasKey('id', $createdProductChromaticity);
        $this->assertNotNull($createdProductChromaticity['id'], 'Created ProductChromaticity must have id specified');
        $this->assertNotNull(ProductChromaticity::find($createdProductChromaticity['id']), 'ProductChromaticity with given id must be in DB');
        $this->assertModelData($productChromaticity, $createdProductChromaticity);
    }

    /**
     * @test read
     */
    public function testReadProductChromaticity()
    {
        $productChromaticity = $this->makeProductChromaticity();
        $dbProductChromaticity = $this->productChromaticityRepo->find($productChromaticity->id);
        $dbProductChromaticity = $dbProductChromaticity->toArray();
        $this->assertModelData($productChromaticity->toArray(), $dbProductChromaticity);
    }

    /**
     * @test update
     */
    public function testUpdateProductChromaticity()
    {
        $productChromaticity = $this->makeProductChromaticity();
        $fakeProductChromaticity = $this->fakeProductChromaticityData();
        $updatedProductChromaticity = $this->productChromaticityRepo->update($fakeProductChromaticity, $productChromaticity->id);
        $this->assertModelData($fakeProductChromaticity, $updatedProductChromaticity->toArray());
        $dbProductChromaticity = $this->productChromaticityRepo->find($productChromaticity->id);
        $this->assertModelData($fakeProductChromaticity, $dbProductChromaticity->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteProductChromaticity()
    {
        $productChromaticity = $this->makeProductChromaticity();
        $resp = $this->productChromaticityRepo->delete($productChromaticity->id);
        $this->assertTrue($resp);
        $this->assertNull(ProductChromaticity::find($productChromaticity->id), 'ProductChromaticity should not exist in DB');
    }
}
