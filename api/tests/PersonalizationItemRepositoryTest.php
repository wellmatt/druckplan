<?php

use App\Models\PersonalizationItem;
use App\Repositories\PersonalizationItemRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PersonalizationItemRepositoryTest extends TestCase
{
    use MakePersonalizationItemTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PersonalizationItemRepository
     */
    protected $personalizationItemRepo;

    public function setUp()
    {
        parent::setUp();
        $this->personalizationItemRepo = App::make(PersonalizationItemRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePersonalizationItem()
    {
        $personalizationItem = $this->fakePersonalizationItemData();
        $createdPersonalizationItem = $this->personalizationItemRepo->create($personalizationItem);
        $createdPersonalizationItem = $createdPersonalizationItem->toArray();
        $this->assertArrayHasKey('id', $createdPersonalizationItem);
        $this->assertNotNull($createdPersonalizationItem['id'], 'Created PersonalizationItem must have id specified');
        $this->assertNotNull(PersonalizationItem::find($createdPersonalizationItem['id']), 'PersonalizationItem with given id must be in DB');
        $this->assertModelData($personalizationItem, $createdPersonalizationItem);
    }

    /**
     * @test read
     */
    public function testReadPersonalizationItem()
    {
        $personalizationItem = $this->makePersonalizationItem();
        $dbPersonalizationItem = $this->personalizationItemRepo->find($personalizationItem->id);
        $dbPersonalizationItem = $dbPersonalizationItem->toArray();
        $this->assertModelData($personalizationItem->toArray(), $dbPersonalizationItem);
    }

    /**
     * @test update
     */
    public function testUpdatePersonalizationItem()
    {
        $personalizationItem = $this->makePersonalizationItem();
        $fakePersonalizationItem = $this->fakePersonalizationItemData();
        $updatedPersonalizationItem = $this->personalizationItemRepo->update($fakePersonalizationItem, $personalizationItem->id);
        $this->assertModelData($fakePersonalizationItem, $updatedPersonalizationItem->toArray());
        $dbPersonalizationItem = $this->personalizationItemRepo->find($personalizationItem->id);
        $this->assertModelData($fakePersonalizationItem, $dbPersonalizationItem->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePersonalizationItem()
    {
        $personalizationItem = $this->makePersonalizationItem();
        $resp = $this->personalizationItemRepo->delete($personalizationItem->id);
        $this->assertTrue($resp);
        $this->assertNull(PersonalizationItem::find($personalizationItem->id), 'PersonalizationItem should not exist in DB');
    }
}
