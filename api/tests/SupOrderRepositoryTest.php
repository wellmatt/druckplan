<?php

use App\Models\SupOrder;
use App\Repositories\SupOrderRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupOrderRepositoryTest extends TestCase
{
    use MakeSupOrderTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupOrderRepository
     */
    protected $supOrderRepo;

    public function setUp()
    {
        parent::setUp();
        $this->supOrderRepo = App::make(SupOrderRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSupOrder()
    {
        $supOrder = $this->fakeSupOrderData();
        $createdSupOrder = $this->supOrderRepo->create($supOrder);
        $createdSupOrder = $createdSupOrder->toArray();
        $this->assertArrayHasKey('id', $createdSupOrder);
        $this->assertNotNull($createdSupOrder['id'], 'Created SupOrder must have id specified');
        $this->assertNotNull(SupOrder::find($createdSupOrder['id']), 'SupOrder with given id must be in DB');
        $this->assertModelData($supOrder, $createdSupOrder);
    }

    /**
     * @test read
     */
    public function testReadSupOrder()
    {
        $supOrder = $this->makeSupOrder();
        $dbSupOrder = $this->supOrderRepo->find($supOrder->id);
        $dbSupOrder = $dbSupOrder->toArray();
        $this->assertModelData($supOrder->toArray(), $dbSupOrder);
    }

    /**
     * @test update
     */
    public function testUpdateSupOrder()
    {
        $supOrder = $this->makeSupOrder();
        $fakeSupOrder = $this->fakeSupOrderData();
        $updatedSupOrder = $this->supOrderRepo->update($fakeSupOrder, $supOrder->id);
        $this->assertModelData($fakeSupOrder, $updatedSupOrder->toArray());
        $dbSupOrder = $this->supOrderRepo->find($supOrder->id);
        $this->assertModelData($fakeSupOrder, $dbSupOrder->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSupOrder()
    {
        $supOrder = $this->makeSupOrder();
        $resp = $this->supOrderRepo->delete($supOrder->id);
        $this->assertTrue($resp);
        $this->assertNull(SupOrder::find($supOrder->id), 'SupOrder should not exist in DB');
    }
}
