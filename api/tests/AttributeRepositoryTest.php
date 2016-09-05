<?php

use App\Models\Attribute;
use App\Repositories\AttributeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AttributeRepositoryTest extends TestCase
{
    use MakeAttributeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AttributeRepository
     */
    protected $attributeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->attributeRepo = App::make(AttributeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAttribute()
    {
        $attribute = $this->fakeAttributeData();
        $createdAttribute = $this->attributeRepo->create($attribute);
        $createdAttribute = $createdAttribute->toArray();
        $this->assertArrayHasKey('id', $createdAttribute);
        $this->assertNotNull($createdAttribute['id'], 'Created Attribute must have id specified');
        $this->assertNotNull(Attribute::find($createdAttribute['id']), 'Attribute with given id must be in DB');
        $this->assertModelData($attribute, $createdAttribute);
    }

    /**
     * @test read
     */
    public function testReadAttribute()
    {
        $attribute = $this->makeAttribute();
        $dbAttribute = $this->attributeRepo->find($attribute->id);
        $dbAttribute = $dbAttribute->toArray();
        $this->assertModelData($attribute->toArray(), $dbAttribute);
    }

    /**
     * @test update
     */
    public function testUpdateAttribute()
    {
        $attribute = $this->makeAttribute();
        $fakeAttribute = $this->fakeAttributeData();
        $updatedAttribute = $this->attributeRepo->update($fakeAttribute, $attribute->id);
        $this->assertModelData($fakeAttribute, $updatedAttribute->toArray());
        $dbAttribute = $this->attributeRepo->find($attribute->id);
        $this->assertModelData($fakeAttribute, $dbAttribute->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAttribute()
    {
        $attribute = $this->makeAttribute();
        $resp = $this->attributeRepo->delete($attribute->id);
        $this->assertTrue($resp);
        $this->assertNull(Attribute::find($attribute->id), 'Attribute should not exist in DB');
    }
}
