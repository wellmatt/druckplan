<?php

use App\Models\OrderMachine;
use App\Repositories\OrderMachineRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderMachineRepositoryTest extends TestCase
{
    use MakeOrderMachineTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var OrderMachineRepository
     */
    protected $orderMachineRepo;

    public function setUp()
    {
        parent::setUp();
        $this->orderMachineRepo = App::make(OrderMachineRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateOrderMachine()
    {
        $orderMachine = $this->fakeOrderMachineData();
        $createdOrderMachine = $this->orderMachineRepo->create($orderMachine);
        $createdOrderMachine = $createdOrderMachine->toArray();
        $this->assertArrayHasKey('id', $createdOrderMachine);
        $this->assertNotNull($createdOrderMachine['id'], 'Created OrderMachine must have id specified');
        $this->assertNotNull(OrderMachine::find($createdOrderMachine['id']), 'OrderMachine with given id must be in DB');
        $this->assertModelData($orderMachine, $createdOrderMachine);
    }

    /**
     * @test read
     */
    public function testReadOrderMachine()
    {
        $orderMachine = $this->makeOrderMachine();
        $dbOrderMachine = $this->orderMachineRepo->find($orderMachine->id);
        $dbOrderMachine = $dbOrderMachine->toArray();
        $this->assertModelData($orderMachine->toArray(), $dbOrderMachine);
    }

    /**
     * @test update
     */
    public function testUpdateOrderMachine()
    {
        $orderMachine = $this->makeOrderMachine();
        $fakeOrderMachine = $this->fakeOrderMachineData();
        $updatedOrderMachine = $this->orderMachineRepo->update($fakeOrderMachine, $orderMachine->id);
        $this->assertModelData($fakeOrderMachine, $updatedOrderMachine->toArray());
        $dbOrderMachine = $this->orderMachineRepo->find($orderMachine->id);
        $this->assertModelData($fakeOrderMachine, $dbOrderMachine->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteOrderMachine()
    {
        $orderMachine = $this->makeOrderMachine();
        $resp = $this->orderMachineRepo->delete($orderMachine->id);
        $this->assertTrue($resp);
        $this->assertNull(OrderMachine::find($orderMachine->id), 'OrderMachine should not exist in DB');
    }
}
