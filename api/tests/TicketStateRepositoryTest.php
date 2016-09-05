<?php

use App\Models\TicketState;
use App\Repositories\TicketStateRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TicketStateRepositoryTest extends TestCase
{
    use MakeTicketStateTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TicketStateRepository
     */
    protected $ticketStateRepo;

    public function setUp()
    {
        parent::setUp();
        $this->ticketStateRepo = App::make(TicketStateRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTicketState()
    {
        $ticketState = $this->fakeTicketStateData();
        $createdTicketState = $this->ticketStateRepo->create($ticketState);
        $createdTicketState = $createdTicketState->toArray();
        $this->assertArrayHasKey('id', $createdTicketState);
        $this->assertNotNull($createdTicketState['id'], 'Created TicketState must have id specified');
        $this->assertNotNull(TicketState::find($createdTicketState['id']), 'TicketState with given id must be in DB');
        $this->assertModelData($ticketState, $createdTicketState);
    }

    /**
     * @test read
     */
    public function testReadTicketState()
    {
        $ticketState = $this->makeTicketState();
        $dbTicketState = $this->ticketStateRepo->find($ticketState->id);
        $dbTicketState = $dbTicketState->toArray();
        $this->assertModelData($ticketState->toArray(), $dbTicketState);
    }

    /**
     * @test update
     */
    public function testUpdateTicketState()
    {
        $ticketState = $this->makeTicketState();
        $fakeTicketState = $this->fakeTicketStateData();
        $updatedTicketState = $this->ticketStateRepo->update($fakeTicketState, $ticketState->id);
        $this->assertModelData($fakeTicketState, $updatedTicketState->toArray());
        $dbTicketState = $this->ticketStateRepo->find($ticketState->id);
        $this->assertModelData($fakeTicketState, $dbTicketState->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTicketState()
    {
        $ticketState = $this->makeTicketState();
        $resp = $this->ticketStateRepo->delete($ticketState->id);
        $this->assertTrue($resp);
        $this->assertNull(TicketState::find($ticketState->id), 'TicketState should not exist in DB');
    }
}
