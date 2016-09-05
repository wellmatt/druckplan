<?php

use App\Models\StorageBookEntry;
use App\Repositories\StorageBookEntryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StorageBookEntryRepositoryTest extends TestCase
{
    use MakeStorageBookEntryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var StorageBookEntryRepository
     */
    protected $storageBookEntryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->storageBookEntryRepo = App::make(StorageBookEntryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateStorageBookEntry()
    {
        $storageBookEntry = $this->fakeStorageBookEntryData();
        $createdStorageBookEntry = $this->storageBookEntryRepo->create($storageBookEntry);
        $createdStorageBookEntry = $createdStorageBookEntry->toArray();
        $this->assertArrayHasKey('id', $createdStorageBookEntry);
        $this->assertNotNull($createdStorageBookEntry['id'], 'Created StorageBookEntry must have id specified');
        $this->assertNotNull(StorageBookEntry::find($createdStorageBookEntry['id']), 'StorageBookEntry with given id must be in DB');
        $this->assertModelData($storageBookEntry, $createdStorageBookEntry);
    }

    /**
     * @test read
     */
    public function testReadStorageBookEntry()
    {
        $storageBookEntry = $this->makeStorageBookEntry();
        $dbStorageBookEntry = $this->storageBookEntryRepo->find($storageBookEntry->id);
        $dbStorageBookEntry = $dbStorageBookEntry->toArray();
        $this->assertModelData($storageBookEntry->toArray(), $dbStorageBookEntry);
    }

    /**
     * @test update
     */
    public function testUpdateStorageBookEntry()
    {
        $storageBookEntry = $this->makeStorageBookEntry();
        $fakeStorageBookEntry = $this->fakeStorageBookEntryData();
        $updatedStorageBookEntry = $this->storageBookEntryRepo->update($fakeStorageBookEntry, $storageBookEntry->id);
        $this->assertModelData($fakeStorageBookEntry, $updatedStorageBookEntry->toArray());
        $dbStorageBookEntry = $this->storageBookEntryRepo->find($storageBookEntry->id);
        $this->assertModelData($fakeStorageBookEntry, $dbStorageBookEntry->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteStorageBookEntry()
    {
        $storageBookEntry = $this->makeStorageBookEntry();
        $resp = $this->storageBookEntryRepo->delete($storageBookEntry->id);
        $this->assertTrue($resp);
        $this->assertNull(StorageBookEntry::find($storageBookEntry->id), 'StorageBookEntry should not exist in DB');
    }
}
