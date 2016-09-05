<?php

use App\Models\TicketLog;
use App\Repositories\TicketLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TicketLogRepositoryTest extends TestCase
{
    use MakeTicketLogTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TicketLogRepository
     */
    protected $ticketLogRepo;

    public function setUp()
    {
        parent::setUp();
        $this->ticketLogRepo = App::make(TicketLogRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTicketLog()
    {
        $ticketLog = $this->fakeTicketLogData();
        $createdTicketLog = $this->ticketLogRepo->create($ticketLog);
        $createdTicketLog = $createdTicketLog->toArray();
        $this->assertArrayHasKey('id', $createdTicketLog);
        $this->assertNotNull($createdTicketLog['id'], 'Created TicketLog must have id specified');
        $this->assertNotNull(TicketLog::find($createdTicketLog['id']), 'TicketLog with given id must be in DB');
        $this->assertModelData($ticketLog, $createdTicketLog);
    }

    /**
     * @test read
     */
    public function testReadTicketLog()
    {
        $ticketLog = $this->makeTicketLog();
        $dbTicketLog = $this->ticketLogRepo->find($ticketLog->id);
        $dbTicketLog = $dbTicketLog->toArray();
        $this->assertModelData($ticketLog->toArray(), $dbTicketLog);
    }

    /**
     * @test update
     */
    public function testUpdateTicketLog()
    {
        $ticketLog = $this->makeTicketLog();
        $fakeTicketLog = $this->fakeTicketLogData();
        $updatedTicketLog = $this->ticketLogRepo->update($fakeTicketLog, $ticketLog->id);
        $this->assertModelData($fakeTicketLog, $updatedTicketLog->toArray());
        $dbTicketLog = $this->ticketLogRepo->find($ticketLog->id);
        $this->assertModelData($fakeTicketLog, $dbTicketLog->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTicketLog()
    {
        $ticketLog = $this->makeTicketLog();
        $resp = $this->ticketLogRepo->delete($ticketLog->id);
        $this->assertTrue($resp);
        $this->assertNull(TicketLog::find($ticketLog->id), 'TicketLog should not exist in DB');
    }
}
