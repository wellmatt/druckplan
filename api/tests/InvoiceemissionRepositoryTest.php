<?php

use App\Models\Invoiceemission;
use App\Repositories\InvoiceemissionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InvoiceemissionRepositoryTest extends TestCase
{
    use MakeInvoiceemissionTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var InvoiceemissionRepository
     */
    protected $invoiceemissionRepo;

    public function setUp()
    {
        parent::setUp();
        $this->invoiceemissionRepo = App::make(InvoiceemissionRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateInvoiceemission()
    {
        $invoiceemission = $this->fakeInvoiceemissionData();
        $createdInvoiceemission = $this->invoiceemissionRepo->create($invoiceemission);
        $createdInvoiceemission = $createdInvoiceemission->toArray();
        $this->assertArrayHasKey('id', $createdInvoiceemission);
        $this->assertNotNull($createdInvoiceemission['id'], 'Created Invoiceemission must have id specified');
        $this->assertNotNull(Invoiceemission::find($createdInvoiceemission['id']), 'Invoiceemission with given id must be in DB');
        $this->assertModelData($invoiceemission, $createdInvoiceemission);
    }

    /**
     * @test read
     */
    public function testReadInvoiceemission()
    {
        $invoiceemission = $this->makeInvoiceemission();
        $dbInvoiceemission = $this->invoiceemissionRepo->find($invoiceemission->id);
        $dbInvoiceemission = $dbInvoiceemission->toArray();
        $this->assertModelData($invoiceemission->toArray(), $dbInvoiceemission);
    }

    /**
     * @test update
     */
    public function testUpdateInvoiceemission()
    {
        $invoiceemission = $this->makeInvoiceemission();
        $fakeInvoiceemission = $this->fakeInvoiceemissionData();
        $updatedInvoiceemission = $this->invoiceemissionRepo->update($fakeInvoiceemission, $invoiceemission->id);
        $this->assertModelData($fakeInvoiceemission, $updatedInvoiceemission->toArray());
        $dbInvoiceemission = $this->invoiceemissionRepo->find($invoiceemission->id);
        $this->assertModelData($fakeInvoiceemission, $dbInvoiceemission->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteInvoiceemission()
    {
        $invoiceemission = $this->makeInvoiceemission();
        $resp = $this->invoiceemissionRepo->delete($invoiceemission->id);
        $this->assertTrue($resp);
        $this->assertNull(Invoiceemission::find($invoiceemission->id), 'Invoiceemission should not exist in DB');
    }
}
