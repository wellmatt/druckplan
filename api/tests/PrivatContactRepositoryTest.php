<?php

use App\Models\PrivatContact;
use App\Repositories\PrivatContactRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PrivatContactRepositoryTest extends TestCase
{
    use MakePrivatContactTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PrivatContactRepository
     */
    protected $privatContactRepo;

    public function setUp()
    {
        parent::setUp();
        $this->privatContactRepo = App::make(PrivatContactRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePrivatContact()
    {
        $privatContact = $this->fakePrivatContactData();
        $createdPrivatContact = $this->privatContactRepo->create($privatContact);
        $createdPrivatContact = $createdPrivatContact->toArray();
        $this->assertArrayHasKey('id', $createdPrivatContact);
        $this->assertNotNull($createdPrivatContact['id'], 'Created PrivatContact must have id specified');
        $this->assertNotNull(PrivatContact::find($createdPrivatContact['id']), 'PrivatContact with given id must be in DB');
        $this->assertModelData($privatContact, $createdPrivatContact);
    }

    /**
     * @test read
     */
    public function testReadPrivatContact()
    {
        $privatContact = $this->makePrivatContact();
        $dbPrivatContact = $this->privatContactRepo->find($privatContact->id);
        $dbPrivatContact = $dbPrivatContact->toArray();
        $this->assertModelData($privatContact->toArray(), $dbPrivatContact);
    }

    /**
     * @test update
     */
    public function testUpdatePrivatContact()
    {
        $privatContact = $this->makePrivatContact();
        $fakePrivatContact = $this->fakePrivatContactData();
        $updatedPrivatContact = $this->privatContactRepo->update($fakePrivatContact, $privatContact->id);
        $this->assertModelData($fakePrivatContact, $updatedPrivatContact->toArray());
        $dbPrivatContact = $this->privatContactRepo->find($privatContact->id);
        $this->assertModelData($fakePrivatContact, $dbPrivatContact->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePrivatContact()
    {
        $privatContact = $this->makePrivatContact();
        $resp = $this->privatContactRepo->delete($privatContact->id);
        $this->assertTrue($resp);
        $this->assertNull(PrivatContact::find($privatContact->id), 'PrivatContact should not exist in DB');
    }
}
