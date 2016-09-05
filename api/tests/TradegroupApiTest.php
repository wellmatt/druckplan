<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TradegroupApiTest extends TestCase
{
    use MakeTradegroupTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTradegroup()
    {
        $tradegroup = $this->fakeTradegroupData();
        $this->json('POST', '/api/v1/tradegroups', $tradegroup);

        $this->assertApiResponse($tradegroup);
    }

    /**
     * @test
     */
    public function testReadTradegroup()
    {
        $tradegroup = $this->makeTradegroup();
        $this->json('GET', '/api/v1/tradegroups/'.$tradegroup->id);

        $this->assertApiResponse($tradegroup->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTradegroup()
    {
        $tradegroup = $this->makeTradegroup();
        $editedTradegroup = $this->fakeTradegroupData();

        $this->json('PUT', '/api/v1/tradegroups/'.$tradegroup->id, $editedTradegroup);

        $this->assertApiResponse($editedTradegroup);
    }

    /**
     * @test
     */
    public function testDeleteTradegroup()
    {
        $tradegroup = $this->makeTradegroup();
        $this->json('DELETE', '/api/v1/tradegroups/'.$tradegroup->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/tradegroups/'.$tradegroup->id);

        $this->assertResponseStatus(404);
    }
}
