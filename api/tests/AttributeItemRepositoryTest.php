<?php

use App\Models\AttributeItem;
use App\Repositories\AttributeItemRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AttributeItemRepositoryTest extends TestCase
{
    use MakeAttributeItemTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AttributeItemRepository
     */
    protected $attributeItemRepo;

    public function setUp()
    {
        parent::setUp();
        $this->attributeItemRepo = App::make(AttributeItemRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAttributeItem()
    {
        $attributeItem = $this->fakeAttributeItemData();
        $createdAttributeItem = $this->attributeItemRepo->create($attributeItem);
        $createdAttributeItem = $createdAttributeItem->toArray();
        $this->assertArrayHasKey('id', $createdAttributeItem);
        $this->assertNotNull($createdAttributeItem['id'], 'Created AttributeItem must have id specified');
        $this->assertNotNull(AttributeItem::find($createdAttributeItem['id']), 'AttributeItem with given id must be in DB');
        $this->assertModelData($attributeItem, $createdAttributeItem);
    }

    /**
     * @test read
     */
    public function testReadAttributeItem()
    {
        $attributeItem = $this->makeAttributeItem();
        $dbAttributeItem = $this->attributeItemRepo->find($attributeItem->id);
        $dbAttributeItem = $dbAttributeItem->toArray();
        $this->assertModelData($attributeItem->toArray(), $dbAttributeItem);
    }

    /**
     * @test update
     */
    public function testUpdateAttributeItem()
    {
        $attributeItem = $this->makeAttributeItem();
        $fakeAttributeItem = $this->fakeAttributeItemData();
        $updatedAttributeItem = $this->attributeItemRepo->update($fakeAttributeItem, $attributeItem->id);
        $this->assertModelData($fakeAttributeItem, $updatedAttributeItem->toArray());
        $dbAttributeItem = $this->attributeItemRepo->find($attributeItem->id);
        $this->assertModelData($fakeAttributeItem, $dbAttributeItem->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAttributeItem()
    {
        $attributeItem = $this->makeAttributeItem();
        $resp = $this->attributeItemRepo->delete($attributeItem->id);
        $this->assertTrue($resp);
        $this->assertNull(AttributeItem::find($attributeItem->id), 'AttributeItem should not exist in DB');
    }
}
