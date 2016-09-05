<?php

use App\Models\TicketCategory;
use App\Repositories\TicketCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TicketCategoryRepositoryTest extends TestCase
{
    use MakeTicketCategoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TicketCategoryRepository
     */
    protected $ticketCategoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->ticketCategoryRepo = App::make(TicketCategoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTicketCategory()
    {
        $ticketCategory = $this->fakeTicketCategoryData();
        $createdTicketCategory = $this->ticketCategoryRepo->create($ticketCategory);
        $createdTicketCategory = $createdTicketCategory->toArray();
        $this->assertArrayHasKey('id', $createdTicketCategory);
        $this->assertNotNull($createdTicketCategory['id'], 'Created TicketCategory must have id specified');
        $this->assertNotNull(TicketCategory::find($createdTicketCategory['id']), 'TicketCategory with given id must be in DB');
        $this->assertModelData($ticketCategory, $createdTicketCategory);
    }

    /**
     * @test read
     */
    public function testReadTicketCategory()
    {
        $ticketCategory = $this->makeTicketCategory();
        $dbTicketCategory = $this->ticketCategoryRepo->find($ticketCategory->id);
        $dbTicketCategory = $dbTicketCategory->toArray();
        $this->assertModelData($ticketCategory->toArray(), $dbTicketCategory);
    }

    /**
     * @test update
     */
    public function testUpdateTicketCategory()
    {
        $ticketCategory = $this->makeTicketCategory();
        $fakeTicketCategory = $this->fakeTicketCategoryData();
        $updatedTicketCategory = $this->ticketCategoryRepo->update($fakeTicketCategory, $ticketCategory->id);
        $this->assertModelData($fakeTicketCategory, $updatedTicketCategory->toArray());
        $dbTicketCategory = $this->ticketCategoryRepo->find($ticketCategory->id);
        $this->assertModelData($fakeTicketCategory, $dbTicketCategory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTicketCategory()
    {
        $ticketCategory = $this->makeTicketCategory();
        $resp = $this->ticketCategoryRepo->delete($ticketCategory->id);
        $this->assertTrue($resp);
        $this->assertNull(TicketCategory::find($ticketCategory->id), 'TicketCategory should not exist in DB');
    }
}
