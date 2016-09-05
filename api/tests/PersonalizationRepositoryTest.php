<?php

use App\Models\Personalization;
use App\Repositories\PersonalizationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PersonalizationRepositoryTest extends TestCase
{
    use MakePersonalizationTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PersonalizationRepository
     */
    protected $personalizationRepo;

    public function setUp()
    {
        parent::setUp();
        $this->personalizationRepo = App::make(PersonalizationRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePersonalization()
    {
        $personalization = $this->fakePersonalizationData();
        $createdPersonalization = $this->personalizationRepo->create($personalization);
        $createdPersonalization = $createdPersonalization->toArray();
        $this->assertArrayHasKey('id', $createdPersonalization);
        $this->assertNotNull($createdPersonalization['id'], 'Created Personalization must have id specified');
        $this->assertNotNull(Personalization::find($createdPersonalization['id']), 'Personalization with given id must be in DB');
        $this->assertModelData($personalization, $createdPersonalization);
    }

    /**
     * @test read
     */
    public function testReadPersonalization()
    {
        $personalization = $this->makePersonalization();
        $dbPersonalization = $this->personalizationRepo->find($personalization->id);
        $dbPersonalization = $dbPersonalization->toArray();
        $this->assertModelData($personalization->toArray(), $dbPersonalization);
    }

    /**
     * @test update
     */
    public function testUpdatePersonalization()
    {
        $personalization = $this->makePersonalization();
        $fakePersonalization = $this->fakePersonalizationData();
        $updatedPersonalization = $this->personalizationRepo->update($fakePersonalization, $personalization->id);
        $this->assertModelData($fakePersonalization, $updatedPersonalization->toArray());
        $dbPersonalization = $this->personalizationRepo->find($personalization->id);
        $this->assertModelData($fakePersonalization, $dbPersonalization->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePersonalization()
    {
        $personalization = $this->makePersonalization();
        $resp = $this->personalizationRepo->delete($personalization->id);
        $this->assertTrue($resp);
        $this->assertNull(Personalization::find($personalization->id), 'Personalization should not exist in DB');
    }
}
