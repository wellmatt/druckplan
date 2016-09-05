<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TicketSourceApiTest extends TestCase
{
    use MakeTicketSourceTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTicketSource()
    {
        $ticketSource = $this->fakeTicketSourceData();
        $this->json('POST', '/api/v1/ticketSources', $ticketSource);

        $this->assertApiResponse($ticketSource);
    }

    /**
     * @test
     */
    public function testReadTicketSource()
    {
        $ticketSource = $this->makeTicketSource();
        $this->json('GET', '/api/v1/ticketSources/'.$ticketSource->id);

        $this->assertApiResponse($ticketSource->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTicketSource()
    {
        $ticketSource = $this->makeTicketSource();
        $editedTicketSource = $this->fakeTicketSourceData();

        $this->json('PUT', '/api/v1/ticketSources/'.$ticketSource->id, $editedTicketSource);

        $this->assertApiResponse($editedTicketSource);
    }

    /**
     * @test
     */
    public function testDeleteTicketSource()
    {
        $ticketSource = $this->makeTicketSource();
        $this->json('DELETE', '/api/v1/ticketSources/'.$ticketSource->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/ticketSources/'.$ticketSource->id);

        $this->assertResponseStatus(404);
    }
}
