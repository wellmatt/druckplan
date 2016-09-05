<?php

use App\Models\PersonalizationOrderItem;
use App\Repositories\PersonalizationOrderItemRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PersonalizationOrderItemRepositoryTest extends TestCase
{
    use MakePersonalizationOrderItemTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PersonalizationOrderItemRepository
     */
    protected $personalizationOrderItemRepo;

    public function setUp()
    {
        parent::setUp();
        $this->personalizationOrderItemRepo = App::make(PersonalizationOrderItemRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePersonalizationOrderItem()
    {
        $personalizationOrderItem = $this->fakePersonalizationOrderItemData();
        $createdPersonalizationOrderItem = $this->personalizationOrderItemRepo->create($personalizationOrderItem);
        $createdPersonalizationOrderItem = $createdPersonalizationOrderItem->toArray();
        $this->assertArrayHasKey('id', $createdPersonalizationOrderItem);
        $this->assertNotNull($createdPersonalizationOrderItem['id'], 'Created PersonalizationOrderItem must have id specified');
        $this->assertNotNull(PersonalizationOrderItem::find($createdPersonalizationOrderItem['id']), 'PersonalizationOrderItem with given id must be in DB');
        $this->assertModelData($personalizationOrderItem, $createdPersonalizationOrderItem);
    }

    /**
     * @test read
     */
    public function testReadPersonalizationOrderItem()
    {
        $personalizationOrderItem = $this->makePersonalizationOrderItem();
        $dbPersonalizationOrderItem = $this->personalizationOrderItemRepo->find($personalizationOrderItem->id);
        $dbPersonalizationOrderItem = $dbPersonalizationOrderItem->toArray();
        $this->assertModelData($personalizationOrderItem->toArray(), $dbPersonalizationOrderItem);
    }

    /**
     * @test update
     */
    public function testUpdatePersonalizationOrderItem()
    {
        $personalizationOrderItem = $this->makePersonalizationOrderItem();
        $fakePersonalizationOrderItem = $this->fakePersonalizationOrderItemData();
        $updatedPersonalizationOrderItem = $this->personalizationOrderItemRepo->update($fakePersonalizationOrderItem, $personalizationOrderItem->id);
        $this->assertModelData($fakePersonalizationOrderItem, $updatedPersonalizationOrderItem->toArray());
        $dbPersonalizationOrderItem = $this->personalizationOrderItemRepo->find($personalizationOrderItem->id);
        $this->assertModelData($fakePersonalizationOrderItem, $dbPersonalizationOrderItem->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePersonalizationOrderItem()
    {
        $personalizationOrderItem = $this->makePersonalizationOrderItem();
        $resp = $this->personalizationOrderItemRepo->delete($personalizationOrderItem->id);
        $this->assertTrue($resp);
        $this->assertNull(PersonalizationOrderItem::find($personalizationOrderItem->id), 'PersonalizationOrderItem should not exist in DB');
    }
}
