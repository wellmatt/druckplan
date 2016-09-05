<?php

use App\Models\ProductPaper;
use App\Repositories\ProductPaperRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductPaperRepositoryTest extends TestCase
{
    use MakeProductPaperTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ProductPaperRepository
     */
    protected $productPaperRepo;

    public function setUp()
    {
        parent::setUp();
        $this->productPaperRepo = App::make(ProductPaperRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateProductPaper()
    {
        $productPaper = $this->fakeProductPaperData();
        $createdProductPaper = $this->productPaperRepo->create($productPaper);
        $createdProductPaper = $createdProductPaper->toArray();
        $this->assertArrayHasKey('id', $createdProductPaper);
        $this->assertNotNull($createdProductPaper['id'], 'Created ProductPaper must have id specified');
        $this->assertNotNull(ProductPaper::find($createdProductPaper['id']), 'ProductPaper with given id must be in DB');
        $this->assertModelData($productPaper, $createdProductPaper);
    }

    /**
     * @test read
     */
    public function testReadProductPaper()
    {
        $productPaper = $this->makeProductPaper();
        $dbProductPaper = $this->productPaperRepo->find($productPaper->id);
        $dbProductPaper = $dbProductPaper->toArray();
        $this->assertModelData($productPaper->toArray(), $dbProductPaper);
    }

    /**
     * @test update
     */
    public function testUpdateProductPaper()
    {
        $productPaper = $this->makeProductPaper();
        $fakeProductPaper = $this->fakeProductPaperData();
        $updatedProductPaper = $this->productPaperRepo->update($fakeProductPaper, $productPaper->id);
        $this->assertModelData($fakeProductPaper, $updatedProductPaper->toArray());
        $dbProductPaper = $this->productPaperRepo->find($productPaper->id);
        $this->assertModelData($fakeProductPaper, $dbProductPaper->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteProductPaper()
    {
        $productPaper = $this->makeProductPaper();
        $resp = $this->productPaperRepo->delete($productPaper->id);
        $this->assertTrue($resp);
        $this->assertNull(ProductPaper::find($productPaper->id), 'ProductPaper should not exist in DB');
    }
}
