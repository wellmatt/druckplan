<?php

use App\Models\Invoicerevert;
use App\Repositories\InvoicerevertRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InvoicerevertRepositoryTest extends TestCase
{
    use MakeInvoicerevertTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var InvoicerevertRepository
     */
    protected $invoicerevertRepo;

    public function setUp()
    {
        parent::setUp();
        $this->invoicerevertRepo = App::make(InvoicerevertRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateInvoicerevert()
    {
        $invoicerevert = $this->fakeInvoicerevertData();
        $createdInvoicerevert = $this->invoicerevertRepo->create($invoicerevert);
        $createdInvoicerevert = $createdInvoicerevert->toArray();
        $this->assertArrayHasKey('id', $createdInvoicerevert);
        $this->assertNotNull($createdInvoicerevert['id'], 'Created Invoicerevert must have id specified');
        $this->assertNotNull(Invoicerevert::find($createdInvoicerevert['id']), 'Invoicerevert with given id must be in DB');
        $this->assertModelData($invoicerevert, $createdInvoicerevert);
    }

    /**
     * @test read
     */
    public function testReadInvoicerevert()
    {
        $invoicerevert = $this->makeInvoicerevert();
        $dbInvoicerevert = $this->invoicerevertRepo->find($invoicerevert->id);
        $dbInvoicerevert = $dbInvoicerevert->toArray();
        $this->assertModelData($invoicerevert->toArray(), $dbInvoicerevert);
    }

    /**
     * @test update
     */
    public function testUpdateInvoicerevert()
    {
        $invoicerevert = $this->makeInvoicerevert();
        $fakeInvoicerevert = $this->fakeInvoicerevertData();
        $updatedInvoicerevert = $this->invoicerevertRepo->update($fakeInvoicerevert, $invoicerevert->id);
        $this->assertModelData($fakeInvoicerevert, $updatedInvoicerevert->toArray());
        $dbInvoicerevert = $this->invoicerevertRepo->find($invoicerevert->id);
        $this->assertModelData($fakeInvoicerevert, $dbInvoicerevert->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteInvoicerevert()
    {
        $invoicerevert = $this->makeInvoicerevert();
        $resp = $this->invoicerevertRepo->delete($invoicerevert->id);
        $this->assertTrue($resp);
        $this->assertNull(Invoicerevert::find($invoicerevert->id), 'Invoicerevert should not exist in DB');
    }
}
