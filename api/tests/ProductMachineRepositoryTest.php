<?php

use App\Models\ProductMachine;
use App\Repositories\ProductMachineRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductMachineRepositoryTest extends TestCase
{
    use MakeProductMachineTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ProductMachineRepository
     */
    protected $productMachineRepo;

    public function setUp()
    {
        parent::setUp();
        $this->productMachineRepo = App::make(ProductMachineRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateProductMachine()
    {
        $productMachine = $this->fakeProductMachineData();
        $createdProductMachine = $this->productMachineRepo->create($productMachine);
        $createdProductMachine = $createdProductMachine->toArray();
        $this->assertArrayHasKey('id', $createdProductMachine);
        $this->assertNotNull($createdProductMachine['id'], 'Created ProductMachine must have id specified');
        $this->assertNotNull(ProductMachine::find($createdProductMachine['id']), 'ProductMachine with given id must be in DB');
        $this->assertModelData($productMachine, $createdProductMachine);
    }

    /**
     * @test read
     */
    public function testReadProductMachine()
    {
        $productMachine = $this->makeProductMachine();
        $dbProductMachine = $this->productMachineRepo->find($productMachine->id);
        $dbProductMachine = $dbProductMachine->toArray();
        $this->assertModelData($productMachine->toArray(), $dbProductMachine);
    }

    /**
     * @test update
     */
    public function testUpdateProductMachine()
    {
        $productMachine = $this->makeProductMachine();
        $fakeProductMachine = $this->fakeProductMachineData();
        $updatedProductMachine = $this->productMachineRepo->update($fakeProductMachine, $productMachine->id);
        $this->assertModelData($fakeProductMachine, $updatedProductMachine->toArray());
        $dbProductMachine = $this->productMachineRepo->find($productMachine->id);
        $this->assertModelData($fakeProductMachine, $dbProductMachine->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteProductMachine()
    {
        $productMachine = $this->makeProductMachine();
        $resp = $this->productMachineRepo->delete($productMachine->id);
        $this->assertTrue($resp);
        $this->assertNull(ProductMachine::find($productMachine->id), 'ProductMachine should not exist in DB');
    }
}
