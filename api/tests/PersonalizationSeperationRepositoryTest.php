<?php

use App\Models\PersonalizationSeperation;
use App\Repositories\PersonalizationSeperationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PersonalizationSeperationRepositoryTest extends TestCase
{
    use MakePersonalizationSeperationTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PersonalizationSeperationRepository
     */
    protected $personalizationSeperationRepo;

    public function setUp()
    {
        parent::setUp();
        $this->personalizationSeperationRepo = App::make(PersonalizationSeperationRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePersonalizationSeperation()
    {
        $personalizationSeperation = $this->fakePersonalizationSeperationData();
        $createdPersonalizationSeperation = $this->personalizationSeperationRepo->create($personalizationSeperation);
        $createdPersonalizationSeperation = $createdPersonalizationSeperation->toArray();
        $this->assertArrayHasKey('id', $createdPersonalizationSeperation);
        $this->assertNotNull($createdPersonalizationSeperation['id'], 'Created PersonalizationSeperation must have id specified');
        $this->assertNotNull(PersonalizationSeperation::find($createdPersonalizationSeperation['id']), 'PersonalizationSeperation with given id must be in DB');
        $this->assertModelData($personalizationSeperation, $createdPersonalizationSeperation);
    }

    /**
     * @test read
     */
    public function testReadPersonalizationSeperation()
    {
        $personalizationSeperation = $this->makePersonalizationSeperation();
        $dbPersonalizationSeperation = $this->personalizationSeperationRepo->find($personalizationSeperation->id);
        $dbPersonalizationSeperation = $dbPersonalizationSeperation->toArray();
        $this->assertModelData($personalizationSeperation->toArray(), $dbPersonalizationSeperation);
    }

    /**
     * @test update
     */
    public function testUpdatePersonalizationSeperation()
    {
        $personalizationSeperation = $this->makePersonalizationSeperation();
        $fakePersonalizationSeperation = $this->fakePersonalizationSeperationData();
        $updatedPersonalizationSeperation = $this->personalizationSeperationRepo->update($fakePersonalizationSeperation, $personalizationSeperation->id);
        $this->assertModelData($fakePersonalizationSeperation, $updatedPersonalizationSeperation->toArray());
        $dbPersonalizationSeperation = $this->personalizationSeperationRepo->find($personalizationSeperation->id);
        $this->assertModelData($fakePersonalizationSeperation, $dbPersonalizationSeperation->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePersonalizationSeperation()
    {
        $personalizationSeperation = $this->makePersonalizationSeperation();
        $resp = $this->personalizationSeperationRepo->delete($personalizationSeperation->id);
        $this->assertTrue($resp);
        $this->assertNull(PersonalizationSeperation::find($personalizationSeperation->id), 'PersonalizationSeperation should not exist in DB');
    }
}
