<?php

use App\Models\Paymentterm;
use App\Repositories\PaymenttermRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaymenttermRepositoryTest extends TestCase
{
    use MakePaymenttermTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaymenttermRepository
     */
    protected $paymenttermRepo;

    public function setUp()
    {
        parent::setUp();
        $this->paymenttermRepo = App::make(PaymenttermRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePaymentterm()
    {
        $paymentterm = $this->fakePaymenttermData();
        $createdPaymentterm = $this->paymenttermRepo->create($paymentterm);
        $createdPaymentterm = $createdPaymentterm->toArray();
        $this->assertArrayHasKey('id', $createdPaymentterm);
        $this->assertNotNull($createdPaymentterm['id'], 'Created Paymentterm must have id specified');
        $this->assertNotNull(Paymentterm::find($createdPaymentterm['id']), 'Paymentterm with given id must be in DB');
        $this->assertModelData($paymentterm, $createdPaymentterm);
    }

    /**
     * @test read
     */
    public function testReadPaymentterm()
    {
        $paymentterm = $this->makePaymentterm();
        $dbPaymentterm = $this->paymenttermRepo->find($paymentterm->id);
        $dbPaymentterm = $dbPaymentterm->toArray();
        $this->assertModelData($paymentterm->toArray(), $dbPaymentterm);
    }

    /**
     * @test update
     */
    public function testUpdatePaymentterm()
    {
        $paymentterm = $this->makePaymentterm();
        $fakePaymentterm = $this->fakePaymenttermData();
        $updatedPaymentterm = $this->paymenttermRepo->update($fakePaymentterm, $paymentterm->id);
        $this->assertModelData($fakePaymentterm, $updatedPaymentterm->toArray());
        $dbPaymentterm = $this->paymenttermRepo->find($paymentterm->id);
        $this->assertModelData($fakePaymentterm, $dbPaymentterm->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePaymentterm()
    {
        $paymentterm = $this->makePaymentterm();
        $resp = $this->paymenttermRepo->delete($paymentterm->id);
        $this->assertTrue($resp);
        $this->assertNull(Paymentterm::find($paymentterm->id), 'Paymentterm should not exist in DB');
    }
}
