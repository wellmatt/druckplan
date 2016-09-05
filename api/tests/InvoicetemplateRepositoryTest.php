<?php

use App\Models\Invoicetemplate;
use App\Repositories\InvoicetemplateRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InvoicetemplateRepositoryTest extends TestCase
{
    use MakeInvoicetemplateTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var InvoicetemplateRepository
     */
    protected $invoicetemplateRepo;

    public function setUp()
    {
        parent::setUp();
        $this->invoicetemplateRepo = App::make(InvoicetemplateRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateInvoicetemplate()
    {
        $invoicetemplate = $this->fakeInvoicetemplateData();
        $createdInvoicetemplate = $this->invoicetemplateRepo->create($invoicetemplate);
        $createdInvoicetemplate = $createdInvoicetemplate->toArray();
        $this->assertArrayHasKey('id', $createdInvoicetemplate);
        $this->assertNotNull($createdInvoicetemplate['id'], 'Created Invoicetemplate must have id specified');
        $this->assertNotNull(Invoicetemplate::find($createdInvoicetemplate['id']), 'Invoicetemplate with given id must be in DB');
        $this->assertModelData($invoicetemplate, $createdInvoicetemplate);
    }

    /**
     * @test read
     */
    public function testReadInvoicetemplate()
    {
        $invoicetemplate = $this->makeInvoicetemplate();
        $dbInvoicetemplate = $this->invoicetemplateRepo->find($invoicetemplate->id);
        $dbInvoicetemplate = $dbInvoicetemplate->toArray();
        $this->assertModelData($invoicetemplate->toArray(), $dbInvoicetemplate);
    }

    /**
     * @test update
     */
    public function testUpdateInvoicetemplate()
    {
        $invoicetemplate = $this->makeInvoicetemplate();
        $fakeInvoicetemplate = $this->fakeInvoicetemplateData();
        $updatedInvoicetemplate = $this->invoicetemplateRepo->update($fakeInvoicetemplate, $invoicetemplate->id);
        $this->assertModelData($fakeInvoicetemplate, $updatedInvoicetemplate->toArray());
        $dbInvoicetemplate = $this->invoicetemplateRepo->find($invoicetemplate->id);
        $this->assertModelData($fakeInvoicetemplate, $dbInvoicetemplate->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteInvoicetemplate()
    {
        $invoicetemplate = $this->makeInvoicetemplate();
        $resp = $this->invoicetemplateRepo->delete($invoicetemplate->id);
        $this->assertTrue($resp);
        $this->assertNull(Invoicetemplate::find($invoicetemplate->id), 'Invoicetemplate should not exist in DB');
    }
}
