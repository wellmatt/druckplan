<?php

use App\Models\PartsListItem;
use App\Repositories\PartsListItemRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PartsListItemRepositoryTest extends TestCase
{
    use MakePartsListItemTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PartsListItemRepository
     */
    protected $partsListItemRepo;

    public function setUp()
    {
        parent::setUp();
        $this->partsListItemRepo = App::make(PartsListItemRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePartsListItem()
    {
        $partsListItem = $this->fakePartsListItemData();
        $createdPartsListItem = $this->partsListItemRepo->create($partsListItem);
        $createdPartsListItem = $createdPartsListItem->toArray();
        $this->assertArrayHasKey('id', $createdPartsListItem);
        $this->assertNotNull($createdPartsListItem['id'], 'Created PartsListItem must have id specified');
        $this->assertNotNull(PartsListItem::find($createdPartsListItem['id']), 'PartsListItem with given id must be in DB');
        $this->assertModelData($partsListItem, $createdPartsListItem);
    }

    /**
     * @test read
     */
    public function testReadPartsListItem()
    {
        $partsListItem = $this->makePartsListItem();
        $dbPartsListItem = $this->partsListItemRepo->find($partsListItem->id);
        $dbPartsListItem = $dbPartsListItem->toArray();
        $this->assertModelData($partsListItem->toArray(), $dbPartsListItem);
    }

    /**
     * @test update
     */
    public function testUpdatePartsListItem()
    {
        $partsListItem = $this->makePartsListItem();
        $fakePartsListItem = $this->fakePartsListItemData();
        $updatedPartsListItem = $this->partsListItemRepo->update($fakePartsListItem, $partsListItem->id);
        $this->assertModelData($fakePartsListItem, $updatedPartsListItem->toArray());
        $dbPartsListItem = $this->partsListItemRepo->find($partsListItem->id);
        $this->assertModelData($fakePartsListItem, $dbPartsListItem->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePartsListItem()
    {
        $partsListItem = $this->makePartsListItem();
        $resp = $this->partsListItemRepo->delete($partsListItem->id);
        $this->assertTrue($resp);
        $this->assertNull(PartsListItem::find($partsListItem->id), 'PartsListItem should not exist in DB');
    }
}
