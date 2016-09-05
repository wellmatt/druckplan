<?php

use App\Models\TicketPriority;
use App\Repositories\TicketPriorityRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TicketPriorityRepositoryTest extends TestCase
{
    use MakeTicketPriorityTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TicketPriorityRepository
     */
    protected $ticketPriorityRepo;

    public function setUp()
    {
        parent::setUp();
        $this->ticketPriorityRepo = App::make(TicketPriorityRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTicketPriority()
    {
        $ticketPriority = $this->fakeTicketPriorityData();
        $createdTicketPriority = $this->ticketPriorityRepo->create($ticketPriority);
        $createdTicketPriority = $createdTicketPriority->toArray();
        $this->assertArrayHasKey('id', $createdTicketPriority);
        $this->assertNotNull($createdTicketPriority['id'], 'Created TicketPriority must have id specified');
        $this->assertNotNull(TicketPriority::find($createdTicketPriority['id']), 'TicketPriority with given id must be in DB');
        $this->assertModelData($ticketPriority, $createdTicketPriority);
    }

    /**
     * @test read
     */
    public function testReadTicketPriority()
    {
        $ticketPriority = $this->makeTicketPriority();
        $dbTicketPriority = $this->ticketPriorityRepo->find($ticketPriority->id);
        $dbTicketPriority = $dbTicketPriority->toArray();
        $this->assertModelData($ticketPriority->toArray(), $dbTicketPriority);
    }

    /**
     * @test update
     */
    public function testUpdateTicketPriority()
    {
        $ticketPriority = $this->makeTicketPriority();
        $fakeTicketPriority = $this->fakeTicketPriorityData();
        $updatedTicketPriority = $this->ticketPriorityRepo->update($fakeTicketPriority, $ticketPriority->id);
        $this->assertModelData($fakeTicketPriority, $updatedTicketPriority->toArray());
        $dbTicketPriority = $this->ticketPriorityRepo->find($ticketPriority->id);
        $this->assertModelData($fakeTicketPriority, $dbTicketPriority->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTicketPriority()
    {
        $ticketPriority = $this->makeTicketPriority();
        $resp = $this->ticketPriorityRepo->delete($ticketPriority->id);
        $this->assertTrue($resp);
        $this->assertNull(TicketPriority::find($ticketPriority->id), 'TicketPriority should not exist in DB');
    }
}
