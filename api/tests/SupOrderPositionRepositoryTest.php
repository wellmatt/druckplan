<?php

use App\Models\SupOrderPosition;
use App\Repositories\SupOrderPositionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupOrderPositionRepositoryTest extends TestCase
{
    use MakeSupOrderPositionTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupOrderPositionRepository
     */
    protected $supOrderPositionRepo;

    public function setUp()
    {
        parent::setUp();
        $this->supOrderPositionRepo = App::make(SupOrderPositionRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSupOrderPosition()
    {
        $supOrderPosition = $this->fakeSupOrderPositionData();
        $createdSupOrderPosition = $this->supOrderPositionRepo->create($supOrderPosition);
        $createdSupOrderPosition = $createdSupOrderPosition->toArray();
        $this->assertArrayHasKey('id', $createdSupOrderPosition);
        $this->assertNotNull($createdSupOrderPosition['id'], 'Created SupOrderPosition must have id specified');
        $this->assertNotNull(SupOrderPosition::find($createdSupOrderPosition['id']), 'SupOrderPosition with given id must be in DB');
        $this->assertModelData($supOrderPosition, $createdSupOrderPosition);
    }

    /**
     * @test read
     */
    public function testReadSupOrderPosition()
    {
        $supOrderPosition = $this->makeSupOrderPosition();
        $dbSupOrderPosition = $this->supOrderPositionRepo->find($supOrderPosition->id);
        $dbSupOrderPosition = $dbSupOrderPosition->toArray();
        $this->assertModelData($supOrderPosition->toArray(), $dbSupOrderPosition);
    }

    /**
     * @test update
     */
    public function testUpdateSupOrderPosition()
    {
        $supOrderPosition = $this->makeSupOrderPosition();
        $fakeSupOrderPosition = $this->fakeSupOrderPositionData();
        $updatedSupOrderPosition = $this->supOrderPositionRepo->update($fakeSupOrderPosition, $supOrderPosition->id);
        $this->assertModelData($fakeSupOrderPosition, $updatedSupOrderPosition->toArray());
        $dbSupOrderPosition = $this->supOrderPositionRepo->find($supOrderPosition->id);
        $this->assertModelData($fakeSupOrderPosition, $dbSupOrderPosition->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSupOrderPosition()
    {
        $supOrderPosition = $this->makeSupOrderPosition();
        $resp = $this->supOrderPositionRepo->delete($supOrderPosition->id);
        $this->assertTrue($resp);
        $this->assertNull(SupOrderPosition::find($supOrderPosition->id), 'SupOrderPosition should not exist in DB');
    }
}
