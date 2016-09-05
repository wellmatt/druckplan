<?php

use App\Models\TicketSource;
use App\Repositories\TicketSourceRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TicketSourceRepositoryTest extends TestCase
{
    use MakeTicketSourceTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TicketSourceRepository
     */
    protected $ticketSourceRepo;

    public function setUp()
    {
        parent::setUp();
        $this->ticketSourceRepo = App::make(TicketSourceRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTicketSource()
    {
        $ticketSource = $this->fakeTicketSourceData();
        $createdTicketSource = $this->ticketSourceRepo->create($ticketSource);
        $createdTicketSource = $createdTicketSource->toArray();
        $this->assertArrayHasKey('id', $createdTicketSource);
        $this->assertNotNull($createdTicketSource['id'], 'Created TicketSource must have id specified');
        $this->assertNotNull(TicketSource::find($createdTicketSource['id']), 'TicketSource with given id must be in DB');
        $this->assertModelData($ticketSource, $createdTicketSource);
    }

    /**
     * @test read
     */
    public function testReadTicketSource()
    {
        $ticketSource = $this->makeTicketSource();
        $dbTicketSource = $this->ticketSourceRepo->find($ticketSource->id);
        $dbTicketSource = $dbTicketSource->toArray();
        $this->assertModelData($ticketSource->toArray(), $dbTicketSource);
    }

    /**
     * @test update
     */
    public function testUpdateTicketSource()
    {
        $ticketSource = $this->makeTicketSource();
        $fakeTicketSource = $this->fakeTicketSourceData();
        $updatedTicketSource = $this->ticketSourceRepo->update($fakeTicketSource, $ticketSource->id);
        $this->assertModelData($fakeTicketSource, $updatedTicketSource->toArray());
        $dbTicketSource = $this->ticketSourceRepo->find($ticketSource->id);
        $this->assertModelData($fakeTicketSource, $dbTicketSource->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTicketSource()
    {
        $ticketSource = $this->makeTicketSource();
        $resp = $this->ticketSourceRepo->delete($ticketSource->id);
        $this->assertTrue($resp);
        $this->assertNull(TicketSource::find($ticketSource->id), 'TicketSource should not exist in DB');
    }
}
