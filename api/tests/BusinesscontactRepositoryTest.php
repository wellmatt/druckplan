<?php

use App\Models\Businesscontact;
use App\Repositories\BusinesscontactRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BusinesscontactRepositoryTest extends TestCase
{
    use MakeBusinesscontactTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BusinesscontactRepository
     */
    protected $businesscontactRepo;

    public function setUp()
    {
        parent::setUp();
        $this->businesscontactRepo = App::make(BusinesscontactRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBusinesscontact()
    {
        $businesscontact = $this->fakeBusinesscontactData();
        $createdBusinesscontact = $this->businesscontactRepo->create($businesscontact);
        $createdBusinesscontact = $createdBusinesscontact->toArray();
        $this->assertArrayHasKey('id', $createdBusinesscontact);
        $this->assertNotNull($createdBusinesscontact['id'], 'Created Businesscontact must have id specified');
        $this->assertNotNull(Businesscontact::find($createdBusinesscontact['id']), 'Businesscontact with given id must be in DB');
        $this->assertModelData($businesscontact, $createdBusinesscontact);
    }

    /**
     * @test read
     */
    public function testReadBusinesscontact()
    {
        $businesscontact = $this->makeBusinesscontact();
        $dbBusinesscontact = $this->businesscontactRepo->find($businesscontact->id);
        $dbBusinesscontact = $dbBusinesscontact->toArray();
        $this->assertModelData($businesscontact->toArray(), $dbBusinesscontact);
    }

    /**
     * @test update
     */
    public function testUpdateBusinesscontact()
    {
        $businesscontact = $this->makeBusinesscontact();
        $fakeBusinesscontact = $this->fakeBusinesscontactData();
        $updatedBusinesscontact = $this->businesscontactRepo->update($fakeBusinesscontact, $businesscontact->id);
        $this->assertModelData($fakeBusinesscontact, $updatedBusinesscontact->toArray());
        $dbBusinesscontact = $this->businesscontactRepo->find($businesscontact->id);
        $this->assertModelData($fakeBusinesscontact, $dbBusinesscontact->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBusinesscontact()
    {
        $businesscontact = $this->makeBusinesscontact();
        $resp = $this->businesscontactRepo->delete($businesscontact->id);
        $this->assertTrue($resp);
        $this->assertNull(Businesscontact::find($businesscontact->id), 'Businesscontact should not exist in DB');
    }
}
