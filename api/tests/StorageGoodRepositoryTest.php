<?php

use App\Models\StorageGood;
use App\Repositories\StorageGoodRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StorageGoodRepositoryTest extends TestCase
{
    use MakeStorageGoodTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var StorageGoodRepository
     */
    protected $storageGoodRepo;

    public function setUp()
    {
        parent::setUp();
        $this->storageGoodRepo = App::make(StorageGoodRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateStorageGood()
    {
        $storageGood = $this->fakeStorageGoodData();
        $createdStorageGood = $this->storageGoodRepo->create($storageGood);
        $createdStorageGood = $createdStorageGood->toArray();
        $this->assertArrayHasKey('id', $createdStorageGood);
        $this->assertNotNull($createdStorageGood['id'], 'Created StorageGood must have id specified');
        $this->assertNotNull(StorageGood::find($createdStorageGood['id']), 'StorageGood with given id must be in DB');
        $this->assertModelData($storageGood, $createdStorageGood);
    }

    /**
     * @test read
     */
    public function testReadStorageGood()
    {
        $storageGood = $this->makeStorageGood();
        $dbStorageGood = $this->storageGoodRepo->find($storageGood->id);
        $dbStorageGood = $dbStorageGood->toArray();
        $this->assertModelData($storageGood->toArray(), $dbStorageGood);
    }

    /**
     * @test update
     */
    public function testUpdateStorageGood()
    {
        $storageGood = $this->makeStorageGood();
        $fakeStorageGood = $this->fakeStorageGoodData();
        $updatedStorageGood = $this->storageGoodRepo->update($fakeStorageGood, $storageGood->id);
        $this->assertModelData($fakeStorageGood, $updatedStorageGood->toArray());
        $dbStorageGood = $this->storageGoodRepo->find($storageGood->id);
        $this->assertModelData($fakeStorageGood, $dbStorageGood->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteStorageGood()
    {
        $storageGood = $this->makeStorageGood();
        $resp = $this->storageGoodRepo->delete($storageGood->id);
        $this->assertTrue($resp);
        $this->assertNull(StorageGood::find($storageGood->id), 'StorageGood should not exist in DB');
    }
}
