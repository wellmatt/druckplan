<?php

use App\Models\StorageGoodPosition;
use App\Repositories\StorageGoodPositionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StorageGoodPositionRepositoryTest extends TestCase
{
    use MakeStorageGoodPositionTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var StorageGoodPositionRepository
     */
    protected $storageGoodPositionRepo;

    public function setUp()
    {
        parent::setUp();
        $this->storageGoodPositionRepo = App::make(StorageGoodPositionRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateStorageGoodPosition()
    {
        $storageGoodPosition = $this->fakeStorageGoodPositionData();
        $createdStorageGoodPosition = $this->storageGoodPositionRepo->create($storageGoodPosition);
        $createdStorageGoodPosition = $createdStorageGoodPosition->toArray();
        $this->assertArrayHasKey('id', $createdStorageGoodPosition);
        $this->assertNotNull($createdStorageGoodPosition['id'], 'Created StorageGoodPosition must have id specified');
        $this->assertNotNull(StorageGoodPosition::find($createdStorageGoodPosition['id']), 'StorageGoodPosition with given id must be in DB');
        $this->assertModelData($storageGoodPosition, $createdStorageGoodPosition);
    }

    /**
     * @test read
     */
    public function testReadStorageGoodPosition()
    {
        $storageGoodPosition = $this->makeStorageGoodPosition();
        $dbStorageGoodPosition = $this->storageGoodPositionRepo->find($storageGoodPosition->id);
        $dbStorageGoodPosition = $dbStorageGoodPosition->toArray();
        $this->assertModelData($storageGoodPosition->toArray(), $dbStorageGoodPosition);
    }

    /**
     * @test update
     */
    public function testUpdateStorageGoodPosition()
    {
        $storageGoodPosition = $this->makeStorageGoodPosition();
        $fakeStorageGoodPosition = $this->fakeStorageGoodPositionData();
        $updatedStorageGoodPosition = $this->storageGoodPositionRepo->update($fakeStorageGoodPosition, $storageGoodPosition->id);
        $this->assertModelData($fakeStorageGoodPosition, $updatedStorageGoodPosition->toArray());
        $dbStorageGoodPosition = $this->storageGoodPositionRepo->find($storageGoodPosition->id);
        $this->assertModelData($fakeStorageGoodPosition, $dbStorageGoodPosition->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteStorageGoodPosition()
    {
        $storageGoodPosition = $this->makeStorageGoodPosition();
        $resp = $this->storageGoodPositionRepo->delete($storageGoodPosition->id);
        $this->assertTrue($resp);
        $this->assertNull(StorageGoodPosition::find($storageGoodPosition->id), 'StorageGoodPosition should not exist in DB');
    }
}
