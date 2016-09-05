<?php

use App\Models\Finishing;
use App\Repositories\FinishingRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FinishingRepositoryTest extends TestCase
{
    use MakeFinishingTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FinishingRepository
     */
    protected $finishingRepo;

    public function setUp()
    {
        parent::setUp();
        $this->finishingRepo = App::make(FinishingRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFinishing()
    {
        $finishing = $this->fakeFinishingData();
        $createdFinishing = $this->finishingRepo->create($finishing);
        $createdFinishing = $createdFinishing->toArray();
        $this->assertArrayHasKey('id', $createdFinishing);
        $this->assertNotNull($createdFinishing['id'], 'Created Finishing must have id specified');
        $this->assertNotNull(Finishing::find($createdFinishing['id']), 'Finishing with given id must be in DB');
        $this->assertModelData($finishing, $createdFinishing);
    }

    /**
     * @test read
     */
    public function testReadFinishing()
    {
        $finishing = $this->makeFinishing();
        $dbFinishing = $this->finishingRepo->find($finishing->id);
        $dbFinishing = $dbFinishing->toArray();
        $this->assertModelData($finishing->toArray(), $dbFinishing);
    }

    /**
     * @test update
     */
    public function testUpdateFinishing()
    {
        $finishing = $this->makeFinishing();
        $fakeFinishing = $this->fakeFinishingData();
        $updatedFinishing = $this->finishingRepo->update($fakeFinishing, $finishing->id);
        $this->assertModelData($fakeFinishing, $updatedFinishing->toArray());
        $dbFinishing = $this->finishingRepo->find($finishing->id);
        $this->assertModelData($fakeFinishing, $dbFinishing->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFinishing()
    {
        $finishing = $this->makeFinishing();
        $resp = $this->finishingRepo->delete($finishing->id);
        $this->assertTrue($resp);
        $this->assertNull(Finishing::find($finishing->id), 'Finishing should not exist in DB');
    }
}
