<?php

use App\Models\OrderCalculation;
use App\Repositories\OrderCalculationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderCalculationRepositoryTest extends TestCase
{
    use MakeOrderCalculationTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var OrderCalculationRepository
     */
    protected $orderCalculationRepo;

    public function setUp()
    {
        parent::setUp();
        $this->orderCalculationRepo = App::make(OrderCalculationRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateOrderCalculation()
    {
        $orderCalculation = $this->fakeOrderCalculationData();
        $createdOrderCalculation = $this->orderCalculationRepo->create($orderCalculation);
        $createdOrderCalculation = $createdOrderCalculation->toArray();
        $this->assertArrayHasKey('id', $createdOrderCalculation);
        $this->assertNotNull($createdOrderCalculation['id'], 'Created OrderCalculation must have id specified');
        $this->assertNotNull(OrderCalculation::find($createdOrderCalculation['id']), 'OrderCalculation with given id must be in DB');
        $this->assertModelData($orderCalculation, $createdOrderCalculation);
    }

    /**
     * @test read
     */
    public function testReadOrderCalculation()
    {
        $orderCalculation = $this->makeOrderCalculation();
        $dbOrderCalculation = $this->orderCalculationRepo->find($orderCalculation->id);
        $dbOrderCalculation = $dbOrderCalculation->toArray();
        $this->assertModelData($orderCalculation->toArray(), $dbOrderCalculation);
    }

    /**
     * @test update
     */
    public function testUpdateOrderCalculation()
    {
        $orderCalculation = $this->makeOrderCalculation();
        $fakeOrderCalculation = $this->fakeOrderCalculationData();
        $updatedOrderCalculation = $this->orderCalculationRepo->update($fakeOrderCalculation, $orderCalculation->id);
        $this->assertModelData($fakeOrderCalculation, $updatedOrderCalculation->toArray());
        $dbOrderCalculation = $this->orderCalculationRepo->find($orderCalculation->id);
        $this->assertModelData($fakeOrderCalculation, $dbOrderCalculation->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteOrderCalculation()
    {
        $orderCalculation = $this->makeOrderCalculation();
        $resp = $this->orderCalculationRepo->delete($orderCalculation->id);
        $this->assertTrue($resp);
        $this->assertNull(OrderCalculation::find($orderCalculation->id), 'OrderCalculation should not exist in DB');
    }
}
