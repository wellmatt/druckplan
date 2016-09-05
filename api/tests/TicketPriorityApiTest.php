<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TicketPriorityApiTest extends TestCase
{
    use MakeTicketPriorityTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTicketPriority()
    {
        $ticketPriority = $this->fakeTicketPriorityData();
        $this->json('POST', '/api/v1/ticketPriorities', $ticketPriority);

        $this->assertApiResponse($ticketPriority);
    }

    /**
     * @test
     */
    public function testReadTicketPriority()
    {
        $ticketPriority = $this->makeTicketPriority();
        $this->json('GET', '/api/v1/ticketPriorities/'.$ticketPriority->id);

        $this->assertApiResponse($ticketPriority->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTicketPriority()
    {
        $ticketPriority = $this->makeTicketPriority();
        $editedTicketPriority = $this->fakeTicketPriorityData();

        $this->json('PUT', '/api/v1/ticketPriorities/'.$ticketPriority->id, $editedTicketPriority);

        $this->assertApiResponse($editedTicketPriority);
    }

    /**
     * @test
     */
    public function testDeleteTicketPriority()
    {
        $ticketPriority = $this->makeTicketPriority();
        $this->json('DELETE', '/api/v1/ticketPriorities/'.$ticketPriority->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/ticketPriorities/'.$ticketPriority->id);

        $this->assertResponseStatus(404);
    }
}
