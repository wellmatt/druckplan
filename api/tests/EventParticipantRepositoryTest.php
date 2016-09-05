<?php

use App\Models\EventParticipant;
use App\Repositories\EventParticipantRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EventParticipantRepositoryTest extends TestCase
{
    use MakeEventParticipantTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var EventParticipantRepository
     */
    protected $eventParticipantRepo;

    public function setUp()
    {
        parent::setUp();
        $this->eventParticipantRepo = App::make(EventParticipantRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateEventParticipant()
    {
        $eventParticipant = $this->fakeEventParticipantData();
        $createdEventParticipant = $this->eventParticipantRepo->create($eventParticipant);
        $createdEventParticipant = $createdEventParticipant->toArray();
        $this->assertArrayHasKey('id', $createdEventParticipant);
        $this->assertNotNull($createdEventParticipant['id'], 'Created EventParticipant must have id specified');
        $this->assertNotNull(EventParticipant::find($createdEventParticipant['id']), 'EventParticipant with given id must be in DB');
        $this->assertModelData($eventParticipant, $createdEventParticipant);
    }

    /**
     * @test read
     */
    public function testReadEventParticipant()
    {
        $eventParticipant = $this->makeEventParticipant();
        $dbEventParticipant = $this->eventParticipantRepo->find($eventParticipant->id);
        $dbEventParticipant = $dbEventParticipant->toArray();
        $this->assertModelData($eventParticipant->toArray(), $dbEventParticipant);
    }

    /**
     * @test update
     */
    public function testUpdateEventParticipant()
    {
        $eventParticipant = $this->makeEventParticipant();
        $fakeEventParticipant = $this->fakeEventParticipantData();
        $updatedEventParticipant = $this->eventParticipantRepo->update($fakeEventParticipant, $eventParticipant->id);
        $this->assertModelData($fakeEventParticipant, $updatedEventParticipant->toArray());
        $dbEventParticipant = $this->eventParticipantRepo->find($eventParticipant->id);
        $this->assertModelData($fakeEventParticipant, $dbEventParticipant->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteEventParticipant()
    {
        $eventParticipant = $this->makeEventParticipant();
        $resp = $this->eventParticipantRepo->delete($eventParticipant->id);
        $this->assertTrue($resp);
        $this->assertNull(EventParticipant::find($eventParticipant->id), 'EventParticipant should not exist in DB');
    }
}
