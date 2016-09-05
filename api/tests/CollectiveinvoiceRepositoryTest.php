<?php

use App\Models\Collectiveinvoice;
use App\Repositories\CollectiveinvoiceRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CollectiveinvoiceRepositoryTest extends TestCase
{
    use MakeCollectiveinvoiceTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CollectiveinvoiceRepository
     */
    protected $collectiveinvoiceRepo;

    public function setUp()
    {
        parent::setUp();
        $this->collectiveinvoiceRepo = App::make(CollectiveinvoiceRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCollectiveinvoice()
    {
        $collectiveinvoice = $this->fakeCollectiveinvoiceData();
        $createdCollectiveinvoice = $this->collectiveinvoiceRepo->create($collectiveinvoice);
        $createdCollectiveinvoice = $createdCollectiveinvoice->toArray();
        $this->assertArrayHasKey('id', $createdCollectiveinvoice);
        $this->assertNotNull($createdCollectiveinvoice['id'], 'Created Collectiveinvoice must have id specified');
        $this->assertNotNull(Collectiveinvoice::find($createdCollectiveinvoice['id']), 'Collectiveinvoice with given id must be in DB');
        $this->assertModelData($collectiveinvoice, $createdCollectiveinvoice);
    }

    /**
     * @test read
     */
    public function testReadCollectiveinvoice()
    {
        $collectiveinvoice = $this->makeCollectiveinvoice();
        $dbCollectiveinvoice = $this->collectiveinvoiceRepo->find($collectiveinvoice->id);
        $dbCollectiveinvoice = $dbCollectiveinvoice->toArray();
        $this->assertModelData($collectiveinvoice->toArray(), $dbCollectiveinvoice);
    }

    /**
     * @test update
     */
    public function testUpdateCollectiveinvoice()
    {
        $collectiveinvoice = $this->makeCollectiveinvoice();
        $fakeCollectiveinvoice = $this->fakeCollectiveinvoiceData();
        $updatedCollectiveinvoice = $this->collectiveinvoiceRepo->update($fakeCollectiveinvoice, $collectiveinvoice->id);
        $this->assertModelData($fakeCollectiveinvoice, $updatedCollectiveinvoice->toArray());
        $dbCollectiveinvoice = $this->collectiveinvoiceRepo->find($collectiveinvoice->id);
        $this->assertModelData($fakeCollectiveinvoice, $dbCollectiveinvoice->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCollectiveinvoice()
    {
        $collectiveinvoice = $this->makeCollectiveinvoice();
        $resp = $this->collectiveinvoiceRepo->delete($collectiveinvoice->id);
        $this->assertTrue($resp);
        $this->assertNull(Collectiveinvoice::find($collectiveinvoice->id), 'Collectiveinvoice should not exist in DB');
    }
}
