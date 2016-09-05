<?php

use App\Models\Contactperson;
use App\Repositories\ContactpersonRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContactpersonRepositoryTest extends TestCase
{
    use MakeContactpersonTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ContactpersonRepository
     */
    protected $contactpersonRepo;

    public function setUp()
    {
        parent::setUp();
        $this->contactpersonRepo = App::make(ContactpersonRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateContactperson()
    {
        $contactperson = $this->fakeContactpersonData();
        $createdContactperson = $this->contactpersonRepo->create($contactperson);
        $createdContactperson = $createdContactperson->toArray();
        $this->assertArrayHasKey('id', $createdContactperson);
        $this->assertNotNull($createdContactperson['id'], 'Created Contactperson must have id specified');
        $this->assertNotNull(Contactperson::find($createdContactperson['id']), 'Contactperson with given id must be in DB');
        $this->assertModelData($contactperson, $createdContactperson);
    }

    /**
     * @test read
     */
    public function testReadContactperson()
    {
        $contactperson = $this->makeContactperson();
        $dbContactperson = $this->contactpersonRepo->find($contactperson->id);
        $dbContactperson = $dbContactperson->toArray();
        $this->assertModelData($contactperson->toArray(), $dbContactperson);
    }

    /**
     * @test update
     */
    public function testUpdateContactperson()
    {
        $contactperson = $this->makeContactperson();
        $fakeContactperson = $this->fakeContactpersonData();
        $updatedContactperson = $this->contactpersonRepo->update($fakeContactperson, $contactperson->id);
        $this->assertModelData($fakeContactperson, $updatedContactperson->toArray());
        $dbContactperson = $this->contactpersonRepo->find($contactperson->id);
        $this->assertModelData($fakeContactperson, $dbContactperson->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteContactperson()
    {
        $contactperson = $this->makeContactperson();
        $resp = $this->contactpersonRepo->delete($contactperson->id);
        $this->assertTrue($resp);
        $this->assertNull(Contactperson::find($contactperson->id), 'Contactperson should not exist in DB');
    }
}
