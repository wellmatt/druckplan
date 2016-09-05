<?php

use App\Models\StoragePosition;
use App\Repositories\StoragePositionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StoragePositionRepositoryTest extends TestCase
{
    use MakeStoragePositionTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var StoragePositionRepository
     */
    protected $storagePositionRepo;

    public function setUp()
    {
        parent::setUp();
        $this->storagePositionRepo = App::make(StoragePositionRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateStoragePosition()
    {
        $storagePosition = $this->fakeStoragePositionData();
        $createdStoragePosition = $this->storagePositionRepo->create($storagePosition);
        $createdStoragePosition = $createdStoragePosition->toArray();
        $this->assertArrayHasKey('id', $createdStoragePosition);
        $this->assertNotNull($createdStoragePosition['id'], 'Created StoragePosition must have id specified');
        $this->assertNotNull(StoragePosition::find($createdStoragePosition['id']), 'StoragePosition with given id must be in DB');
        $this->assertModelData($storagePosition, $createdStoragePosition);
    }

    /**
     * @test read
     */
    public function testReadStoragePosition()
    {
        $storagePosition = $this->makeStoragePosition();
        $dbStoragePosition = $this->storagePositionRepo->find($storagePosition->id);
        $dbStoragePosition = $dbStoragePosition->toArray();
        $this->assertModelData($storagePosition->toArray(), $dbStoragePosition);
    }

    /**
     * @test update
     */
    public function testUpdateStoragePosition()
    {
        $storagePosition = $this->makeStoragePosition();
        $fakeStoragePosition = $this->fakeStoragePositionData();
        $updatedStoragePosition = $this->storagePositionRepo->update($fakeStoragePosition, $storagePosition->id);
        $this->assertModelData($fakeStoragePosition, $updatedStoragePosition->toArray());
        $dbStoragePosition = $this->storagePositionRepo->find($storagePosition->id);
        $this->assertModelData($fakeStoragePosition, $dbStoragePosition->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteStoragePosition()
    {
        $storagePosition = $this->makeStoragePosition();
        $resp = $this->storagePositionRepo->delete($storagePosition->id);
        $this->assertTrue($resp);
        $this->assertNull(StoragePosition::find($storagePosition->id), 'StoragePosition should not exist in DB');
    }
}
