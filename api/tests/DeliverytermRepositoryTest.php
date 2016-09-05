<?php

use App\Models\Deliveryterm;
use App\Repositories\DeliverytermRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeliverytermRepositoryTest extends TestCase
{
    use MakeDeliverytermTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DeliverytermRepository
     */
    protected $deliverytermRepo;

    public function setUp()
    {
        parent::setUp();
        $this->deliverytermRepo = App::make(DeliverytermRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDeliveryterm()
    {
        $deliveryterm = $this->fakeDeliverytermData();
        $createdDeliveryterm = $this->deliverytermRepo->create($deliveryterm);
        $createdDeliveryterm = $createdDeliveryterm->toArray();
        $this->assertArrayHasKey('id', $createdDeliveryterm);
        $this->assertNotNull($createdDeliveryterm['id'], 'Created Deliveryterm must have id specified');
        $this->assertNotNull(Deliveryterm::find($createdDeliveryterm['id']), 'Deliveryterm with given id must be in DB');
        $this->assertModelData($deliveryterm, $createdDeliveryterm);
    }

    /**
     * @test read
     */
    public function testReadDeliveryterm()
    {
        $deliveryterm = $this->makeDeliveryterm();
        $dbDeliveryterm = $this->deliverytermRepo->find($deliveryterm->id);
        $dbDeliveryterm = $dbDeliveryterm->toArray();
        $this->assertModelData($deliveryterm->toArray(), $dbDeliveryterm);
    }

    /**
     * @test update
     */
    public function testUpdateDeliveryterm()
    {
        $deliveryterm = $this->makeDeliveryterm();
        $fakeDeliveryterm = $this->fakeDeliverytermData();
        $updatedDeliveryterm = $this->deliverytermRepo->update($fakeDeliveryterm, $deliveryterm->id);
        $this->assertModelData($fakeDeliveryterm, $updatedDeliveryterm->toArray());
        $dbDeliveryterm = $this->deliverytermRepo->find($deliveryterm->id);
        $this->assertModelData($fakeDeliveryterm, $dbDeliveryterm->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDeliveryterm()
    {
        $deliveryterm = $this->makeDeliveryterm();
        $resp = $this->deliverytermRepo->delete($deliveryterm->id);
        $this->assertTrue($resp);
        $this->assertNull(Deliveryterm::find($deliveryterm->id), 'Deliveryterm should not exist in DB');
    }
}
