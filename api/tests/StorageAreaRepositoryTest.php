<?php

use App\Models\StorageArea;
use App\Repositories\StorageAreaRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StorageAreaRepositoryTest extends TestCase
{
    use MakeStorageAreaTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var StorageAreaRepository
     */
    protected $storageAreaRepo;

    public function setUp()
    {
        parent::setUp();
        $this->storageAreaRepo = App::make(StorageAreaRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateStorageArea()
    {
        $storageArea = $this->fakeStorageAreaData();
        $createdStorageArea = $this->storageAreaRepo->create($storageArea);
        $createdStorageArea = $createdStorageArea->toArray();
        $this->assertArrayHasKey('id', $createdStorageArea);
        $this->assertNotNull($createdStorageArea['id'], 'Created StorageArea must have id specified');
        $this->assertNotNull(StorageArea::find($createdStorageArea['id']), 'StorageArea with given id must be in DB');
        $this->assertModelData($storageArea, $createdStorageArea);
    }

    /**
     * @test read
     */
    public function testReadStorageArea()
    {
        $storageArea = $this->makeStorageArea();
        $dbStorageArea = $this->storageAreaRepo->find($storageArea->id);
        $dbStorageArea = $dbStorageArea->toArray();
        $this->assertModelData($storageArea->toArray(), $dbStorageArea);
    }

    /**
     * @test update
     */
    public function testUpdateStorageArea()
    {
        $storageArea = $this->makeStorageArea();
        $fakeStorageArea = $this->fakeStorageAreaData();
        $updatedStorageArea = $this->storageAreaRepo->update($fakeStorageArea, $storageArea->id);
        $this->assertModelData($fakeStorageArea, $updatedStorageArea->toArray());
        $dbStorageArea = $this->storageAreaRepo->find($storageArea->id);
        $this->assertModelData($fakeStorageArea, $dbStorageArea->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteStorageArea()
    {
        $storageArea = $this->makeStorageArea();
        $resp = $this->storageAreaRepo->delete($storageArea->id);
        $this->assertTrue($resp);
        $this->assertNull(StorageArea::find($storageArea->id), 'StorageArea should not exist in DB');
    }
}
