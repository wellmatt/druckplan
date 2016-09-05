<?php

use App\Models\PaperPrice;
use App\Repositories\PaperPriceRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaperPriceRepositoryTest extends TestCase
{
    use MakePaperPriceTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaperPriceRepository
     */
    protected $paperPriceRepo;

    public function setUp()
    {
        parent::setUp();
        $this->paperPriceRepo = App::make(PaperPriceRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePaperPrice()
    {
        $paperPrice = $this->fakePaperPriceData();
        $createdPaperPrice = $this->paperPriceRepo->create($paperPrice);
        $createdPaperPrice = $createdPaperPrice->toArray();
        $this->assertArrayHasKey('id', $createdPaperPrice);
        $this->assertNotNull($createdPaperPrice['id'], 'Created PaperPrice must have id specified');
        $this->assertNotNull(PaperPrice::find($createdPaperPrice['id']), 'PaperPrice with given id must be in DB');
        $this->assertModelData($paperPrice, $createdPaperPrice);
    }

    /**
     * @test read
     */
    public function testReadPaperPrice()
    {
        $paperPrice = $this->makePaperPrice();
        $dbPaperPrice = $this->paperPriceRepo->find($paperPrice->id);
        $dbPaperPrice = $dbPaperPrice->toArray();
        $this->assertModelData($paperPrice->toArray(), $dbPaperPrice);
    }

    /**
     * @test update
     */
    public function testUpdatePaperPrice()
    {
        $paperPrice = $this->makePaperPrice();
        $fakePaperPrice = $this->fakePaperPriceData();
        $updatedPaperPrice = $this->paperPriceRepo->update($fakePaperPrice, $paperPrice->id);
        $this->assertModelData($fakePaperPrice, $updatedPaperPrice->toArray());
        $dbPaperPrice = $this->paperPriceRepo->find($paperPrice->id);
        $this->assertModelData($fakePaperPrice, $dbPaperPrice->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePaperPrice()
    {
        $paperPrice = $this->makePaperPrice();
        $resp = $this->paperPriceRepo->delete($paperPrice->id);
        $this->assertTrue($resp);
        $this->assertNull(PaperPrice::find($paperPrice->id), 'PaperPrice should not exist in DB');
    }
}
