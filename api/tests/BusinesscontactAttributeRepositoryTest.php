<?php

use App\Models\BusinesscontactAttribute;
use App\Repositories\BusinesscontactAttributeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BusinesscontactAttributeRepositoryTest extends TestCase
{
    use MakeBusinesscontactAttributeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BusinesscontactAttributeRepository
     */
    protected $businesscontactAttributeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->businesscontactAttributeRepo = App::make(BusinesscontactAttributeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBusinesscontactAttribute()
    {
        $businesscontactAttribute = $this->fakeBusinesscontactAttributeData();
        $createdBusinesscontactAttribute = $this->businesscontactAttributeRepo->create($businesscontactAttribute);
        $createdBusinesscontactAttribute = $createdBusinesscontactAttribute->toArray();
        $this->assertArrayHasKey('id', $createdBusinesscontactAttribute);
        $this->assertNotNull($createdBusinesscontactAttribute['id'], 'Created BusinesscontactAttribute must have id specified');
        $this->assertNotNull(BusinesscontactAttribute::find($createdBusinesscontactAttribute['id']), 'BusinesscontactAttribute with given id must be in DB');
        $this->assertModelData($businesscontactAttribute, $createdBusinesscontactAttribute);
    }

    /**
     * @test read
     */
    public function testReadBusinesscontactAttribute()
    {
        $businesscontactAttribute = $this->makeBusinesscontactAttribute();
        $dbBusinesscontactAttribute = $this->businesscontactAttributeRepo->find($businesscontactAttribute->id);
        $dbBusinesscontactAttribute = $dbBusinesscontactAttribute->toArray();
        $this->assertModelData($businesscontactAttribute->toArray(), $dbBusinesscontactAttribute);
    }

    /**
     * @test update
     */
    public function testUpdateBusinesscontactAttribute()
    {
        $businesscontactAttribute = $this->makeBusinesscontactAttribute();
        $fakeBusinesscontactAttribute = $this->fakeBusinesscontactAttributeData();
        $updatedBusinesscontactAttribute = $this->businesscontactAttributeRepo->update($fakeBusinesscontactAttribute, $businesscontactAttribute->id);
        $this->assertModelData($fakeBusinesscontactAttribute, $updatedBusinesscontactAttribute->toArray());
        $dbBusinesscontactAttribute = $this->businesscontactAttributeRepo->find($businesscontactAttribute->id);
        $this->assertModelData($fakeBusinesscontactAttribute, $dbBusinesscontactAttribute->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBusinesscontactAttribute()
    {
        $businesscontactAttribute = $this->makeBusinesscontactAttribute();
        $resp = $this->businesscontactAttributeRepo->delete($businesscontactAttribute->id);
        $this->assertTrue($resp);
        $this->assertNull(BusinesscontactAttribute::find($businesscontactAttribute->id), 'BusinesscontactAttribute should not exist in DB');
    }
}
