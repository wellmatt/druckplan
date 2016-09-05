<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TicketCategoryApiTest extends TestCase
{
    use MakeTicketCategoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTicketCategory()
    {
        $ticketCategory = $this->fakeTicketCategoryData();
        $this->json('POST', '/api/v1/ticketCategories', $ticketCategory);

        $this->assertApiResponse($ticketCategory);
    }

    /**
     * @test
     */
    public function testReadTicketCategory()
    {
        $ticketCategory = $this->makeTicketCategory();
        $this->json('GET', '/api/v1/ticketCategories/'.$ticketCategory->id);

        $this->assertApiResponse($ticketCategory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTicketCategory()
    {
        $ticketCategory = $this->makeTicketCategory();
        $editedTicketCategory = $this->fakeTicketCategoryData();

        $this->json('PUT', '/api/v1/ticketCategories/'.$ticketCategory->id, $editedTicketCategory);

        $this->assertApiResponse($editedTicketCategory);
    }

    /**
     * @test
     */
    public function testDeleteTicketCategory()
    {
        $ticketCategory = $this->makeTicketCategory();
        $this->json('DELETE', '/api/v1/ticketCategories/'.$ticketCategory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/ticketCategories/'.$ticketCategory->id);

        $this->assertResponseStatus(404);
    }
}
