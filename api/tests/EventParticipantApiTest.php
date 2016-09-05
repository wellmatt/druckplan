<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EventParticipantApiTest extends TestCase
{
    use MakeEventParticipantTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateEventParticipant()
    {
        $eventParticipant = $this->fakeEventParticipantData();
        $this->json('POST', '/api/v1/eventParticipants', $eventParticipant);

        $this->assertApiResponse($eventParticipant);
    }

    /**
     * @test
     */
    public function testReadEventParticipant()
    {
        $eventParticipant = $this->makeEventParticipant();
        $this->json('GET', '/api/v1/eventParticipants/'.$eventParticipant->id);

        $this->assertApiResponse($eventParticipant->toArray());
    }

    /**
     * @test
     */
    public function testUpdateEventParticipant()
    {
        $eventParticipant = $this->makeEventParticipant();
        $editedEventParticipant = $this->fakeEventParticipantData();

        $this->json('PUT', '/api/v1/eventParticipants/'.$eventParticipant->id, $editedEventParticipant);

        $this->assertApiResponse($editedEventParticipant);
    }

    /**
     * @test
     */
    public function testDeleteEventParticipant()
    {
        $eventParticipant = $this->makeEventParticipant();
        $this->json('DELETE', '/api/v1/eventParticipants/'.$eventParticipant->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/eventParticipants/'.$eventParticipant->id);

        $this->assertResponseStatus(404);
    }
}
