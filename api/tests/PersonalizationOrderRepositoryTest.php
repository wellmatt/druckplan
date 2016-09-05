<?php

use App\Models\PersonalizationOrder;
use App\Repositories\PersonalizationOrderRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PersonalizationOrderRepositoryTest extends TestCase
{
    use MakePersonalizationOrderTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PersonalizationOrderRepository
     */
    protected $personalizationOrderRepo;

    public function setUp()
    {
        parent::setUp();
        $this->personalizationOrderRepo = App::make(PersonalizationOrderRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePersonalizationOrder()
    {
        $personalizationOrder = $this->fakePersonalizationOrderData();
        $createdPersonalizationOrder = $this->personalizationOrderRepo->create($personalizationOrder);
        $createdPersonalizationOrder = $createdPersonalizationOrder->toArray();
        $this->assertArrayHasKey('id', $createdPersonalizationOrder);
        $this->assertNotNull($createdPersonalizationOrder['id'], 'Created PersonalizationOrder must have id specified');
        $this->assertNotNull(PersonalizationOrder::find($createdPersonalizationOrder['id']), 'PersonalizationOrder with given id must be in DB');
        $this->assertModelData($personalizationOrder, $createdPersonalizationOrder);
    }

    /**
     * @test read
     */
    public function testReadPersonalizationOrder()
    {
        $personalizationOrder = $this->makePersonalizationOrder();
        $dbPersonalizationOrder = $this->personalizationOrderRepo->find($personalizationOrder->id);
        $dbPersonalizationOrder = $dbPersonalizationOrder->toArray();
        $this->assertModelData($personalizationOrder->toArray(), $dbPersonalizationOrder);
    }

    /**
     * @test update
     */
    public function testUpdatePersonalizationOrder()
    {
        $personalizationOrder = $this->makePersonalizationOrder();
        $fakePersonalizationOrder = $this->fakePersonalizationOrderData();
        $updatedPersonalizationOrder = $this->personalizationOrderRepo->update($fakePersonalizationOrder, $personalizationOrder->id);
        $this->assertModelData($fakePersonalizationOrder, $updatedPersonalizationOrder->toArray());
        $dbPersonalizationOrder = $this->personalizationOrderRepo->find($personalizationOrder->id);
        $this->assertModelData($fakePersonalizationOrder, $dbPersonalizationOrder->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePersonalizationOrder()
    {
        $personalizationOrder = $this->makePersonalizationOrder();
        $resp = $this->personalizationOrderRepo->delete($personalizationOrder->id);
        $this->assertTrue($resp);
        $this->assertNull(PersonalizationOrder::find($personalizationOrder->id), 'PersonalizationOrder should not exist in DB');
    }
}
