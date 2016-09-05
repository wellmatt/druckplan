<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TicketLogApiTest extends TestCase
{
    use MakeTicketLogTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTicketLog()
    {
        $ticketLog = $this->fakeTicketLogData();
        $this->json('POST', '/api/v1/ticketLogs', $ticketLog);

        $this->assertApiResponse($ticketLog);
    }

    /**
     * @test
     */
    public function testReadTicketLog()
    {
        $ticketLog = $this->makeTicketLog();
        $this->json('GET', '/api/v1/ticketLogs/'.$ticketLog->id);

        $this->assertApiResponse($ticketLog->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTicketLog()
    {
        $ticketLog = $this->makeTicketLog();
        $editedTicketLog = $this->fakeTicketLogData();

        $this->json('PUT', '/api/v1/ticketLogs/'.$ticketLog->id, $editedTicketLog);

        $this->assertApiResponse($editedTicketLog);
    }

    /**
     * @test
     */
    public function testDeleteTicketLog()
    {
        $ticketLog = $this->makeTicketLog();
        $this->json('DELETE', '/api/v1/ticketLogs/'.$ticketLog->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/ticketLogs/'.$ticketLog->id);

        $this->assertResponseStatus(404);
    }
}
