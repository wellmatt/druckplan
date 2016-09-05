<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TicketStateApiTest extends TestCase
{
    use MakeTicketStateTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTicketState()
    {
        $ticketState = $this->fakeTicketStateData();
        $this->json('POST', '/api/v1/ticketStates', $ticketState);

        $this->assertApiResponse($ticketState);
    }

    /**
     * @test
     */
    public function testReadTicketState()
    {
        $ticketState = $this->makeTicketState();
        $this->json('GET', '/api/v1/ticketStates/'.$ticketState->id);

        $this->assertApiResponse($ticketState->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTicketState()
    {
        $ticketState = $this->makeTicketState();
        $editedTicketState = $this->fakeTicketStateData();

        $this->json('PUT', '/api/v1/ticketStates/'.$ticketState->id, $editedTicketState);

        $this->assertApiResponse($editedTicketState);
    }

    /**
     * @test
     */
    public function testDeleteTicketState()
    {
        $ticketState = $this->makeTicketState();
        $this->json('DELETE', '/api/v1/ticketStates/'.$ticketState->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/ticketStates/'.$ticketState->id);

        $this->assertResponseStatus(404);
    }
}
