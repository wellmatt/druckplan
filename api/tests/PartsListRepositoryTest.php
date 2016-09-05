<?php

use App\Models\PartsList;
use App\Repositories\PartsListRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PartsListRepositoryTest extends TestCase
{
    use MakePartsListTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PartsListRepository
     */
    protected $partsListRepo;

    public function setUp()
    {
        parent::setUp();
        $this->partsListRepo = App::make(PartsListRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePartsList()
    {
        $partsList = $this->fakePartsListData();
        $createdPartsList = $this->partsListRepo->create($partsList);
        $createdPartsList = $createdPartsList->toArray();
        $this->assertArrayHasKey('id', $createdPartsList);
        $this->assertNotNull($createdPartsList['id'], 'Created PartsList must have id specified');
        $this->assertNotNull(PartsList::find($createdPartsList['id']), 'PartsList with given id must be in DB');
        $this->assertModelData($partsList, $createdPartsList);
    }

    /**
     * @test read
     */
    public function testReadPartsList()
    {
        $partsList = $this->makePartsList();
        $dbPartsList = $this->partsListRepo->find($partsList->id);
        $dbPartsList = $dbPartsList->toArray();
        $this->assertModelData($partsList->toArray(), $dbPartsList);
    }

    /**
     * @test update
     */
    public function testUpdatePartsList()
    {
        $partsList = $this->makePartsList();
        $fakePartsList = $this->fakePartsListData();
        $updatedPartsList = $this->partsListRepo->update($fakePartsList, $partsList->id);
        $this->assertModelData($fakePartsList, $updatedPartsList->toArray());
        $dbPartsList = $this->partsListRepo->find($partsList->id);
        $this->assertModelData($fakePartsList, $dbPartsList->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePartsList()
    {
        $partsList = $this->makePartsList();
        $resp = $this->partsListRepo->delete($partsList->id);
        $this->assertTrue($resp);
        $this->assertNull(PartsList::find($partsList->id), 'PartsList should not exist in DB');
    }
}
