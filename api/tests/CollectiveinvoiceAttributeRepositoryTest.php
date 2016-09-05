<?php

use App\Models\CollectiveinvoiceAttribute;
use App\Repositories\CollectiveinvoiceAttributeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CollectiveinvoiceAttributeRepositoryTest extends TestCase
{
    use MakeCollectiveinvoiceAttributeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CollectiveinvoiceAttributeRepository
     */
    protected $collectiveinvoiceAttributeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->collectiveinvoiceAttributeRepo = App::make(CollectiveinvoiceAttributeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCollectiveinvoiceAttribute()
    {
        $collectiveinvoiceAttribute = $this->fakeCollectiveinvoiceAttributeData();
        $createdCollectiveinvoiceAttribute = $this->collectiveinvoiceAttributeRepo->create($collectiveinvoiceAttribute);
        $createdCollectiveinvoiceAttribute = $createdCollectiveinvoiceAttribute->toArray();
        $this->assertArrayHasKey('id', $createdCollectiveinvoiceAttribute);
        $this->assertNotNull($createdCollectiveinvoiceAttribute['id'], 'Created CollectiveinvoiceAttribute must have id specified');
        $this->assertNotNull(CollectiveinvoiceAttribute::find($createdCollectiveinvoiceAttribute['id']), 'CollectiveinvoiceAttribute with given id must be in DB');
        $this->assertModelData($collectiveinvoiceAttribute, $createdCollectiveinvoiceAttribute);
    }

    /**
     * @test read
     */
    public function testReadCollectiveinvoiceAttribute()
    {
        $collectiveinvoiceAttribute = $this->makeCollectiveinvoiceAttribute();
        $dbCollectiveinvoiceAttribute = $this->collectiveinvoiceAttributeRepo->find($collectiveinvoiceAttribute->id);
        $dbCollectiveinvoiceAttribute = $dbCollectiveinvoiceAttribute->toArray();
        $this->assertModelData($collectiveinvoiceAttribute->toArray(), $dbCollectiveinvoiceAttribute);
    }

    /**
     * @test update
     */
    public function testUpdateCollectiveinvoiceAttribute()
    {
        $collectiveinvoiceAttribute = $this->makeCollectiveinvoiceAttribute();
        $fakeCollectiveinvoiceAttribute = $this->fakeCollectiveinvoiceAttributeData();
        $updatedCollectiveinvoiceAttribute = $this->collectiveinvoiceAttributeRepo->update($fakeCollectiveinvoiceAttribute, $collectiveinvoiceAttribute->id);
        $this->assertModelData($fakeCollectiveinvoiceAttribute, $updatedCollectiveinvoiceAttribute->toArray());
        $dbCollectiveinvoiceAttribute = $this->collectiveinvoiceAttributeRepo->find($collectiveinvoiceAttribute->id);
        $this->assertModelData($fakeCollectiveinvoiceAttribute, $dbCollectiveinvoiceAttribute->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCollectiveinvoiceAttribute()
    {
        $collectiveinvoiceAttribute = $this->makeCollectiveinvoiceAttribute();
        $resp = $this->collectiveinvoiceAttributeRepo->delete($collectiveinvoiceAttribute->id);
        $this->assertTrue($resp);
        $this->assertNull(CollectiveinvoiceAttribute::find($collectiveinvoiceAttribute->id), 'CollectiveinvoiceAttribute should not exist in DB');
    }
}
