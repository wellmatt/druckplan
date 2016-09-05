<?php

use App\Models\Tradegroup;
use App\Repositories\TradegroupRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TradegroupRepositoryTest extends TestCase
{
    use MakeTradegroupTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TradegroupRepository
     */
    protected $tradegroupRepo;

    public function setUp()
    {
        parent::setUp();
        $this->tradegroupRepo = App::make(TradegroupRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTradegroup()
    {
        $tradegroup = $this->fakeTradegroupData();
        $createdTradegroup = $this->tradegroupRepo->create($tradegroup);
        $createdTradegroup = $createdTradegroup->toArray();
        $this->assertArrayHasKey('id', $createdTradegroup);
        $this->assertNotNull($createdTradegroup['id'], 'Created Tradegroup must have id specified');
        $this->assertNotNull(Tradegroup::find($createdTradegroup['id']), 'Tradegroup with given id must be in DB');
        $this->assertModelData($tradegroup, $createdTradegroup);
    }

    /**
     * @test read
     */
    public function testReadTradegroup()
    {
        $tradegroup = $this->makeTradegroup();
        $dbTradegroup = $this->tradegroupRepo->find($tradegroup->id);
        $dbTradegroup = $dbTradegroup->toArray();
        $this->assertModelData($tradegroup->toArray(), $dbTradegroup);
    }

    /**
     * @test update
     */
    public function testUpdateTradegroup()
    {
        $tradegroup = $this->makeTradegroup();
        $fakeTradegroup = $this->fakeTradegroupData();
        $updatedTradegroup = $this->tradegroupRepo->update($fakeTradegroup, $tradegroup->id);
        $this->assertModelData($fakeTradegroup, $updatedTradegroup->toArray());
        $dbTradegroup = $this->tradegroupRepo->find($tradegroup->id);
        $this->assertModelData($fakeTradegroup, $dbTradegroup->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTradegroup()
    {
        $tradegroup = $this->makeTradegroup();
        $resp = $this->tradegroupRepo->delete($tradegroup->id);
        $this->assertTrue($resp);
        $this->assertNull(Tradegroup::find($tradegroup->id), 'Tradegroup should not exist in DB');
    }
}
