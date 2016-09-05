<?php

use App\Models\PrivatContactAccess;
use App\Repositories\PrivatContactAccessRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PrivatContactAccessRepositoryTest extends TestCase
{
    use MakePrivatContactAccessTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PrivatContactAccessRepository
     */
    protected $privatContactAccessRepo;

    public function setUp()
    {
        parent::setUp();
        $this->privatContactAccessRepo = App::make(PrivatContactAccessRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePrivatContactAccess()
    {
        $privatContactAccess = $this->fakePrivatContactAccessData();
        $createdPrivatContactAccess = $this->privatContactAccessRepo->create($privatContactAccess);
        $createdPrivatContactAccess = $createdPrivatContactAccess->toArray();
        $this->assertArrayHasKey('id', $createdPrivatContactAccess);
        $this->assertNotNull($createdPrivatContactAccess['id'], 'Created PrivatContactAccess must have id specified');
        $this->assertNotNull(PrivatContactAccess::find($createdPrivatContactAccess['id']), 'PrivatContactAccess with given id must be in DB');
        $this->assertModelData($privatContactAccess, $createdPrivatContactAccess);
    }

    /**
     * @test read
     */
    public function testReadPrivatContactAccess()
    {
        $privatContactAccess = $this->makePrivatContactAccess();
        $dbPrivatContactAccess = $this->privatContactAccessRepo->find($privatContactAccess->id);
        $dbPrivatContactAccess = $dbPrivatContactAccess->toArray();
        $this->assertModelData($privatContactAccess->toArray(), $dbPrivatContactAccess);
    }

    /**
     * @test update
     */
    public function testUpdatePrivatContactAccess()
    {
        $privatContactAccess = $this->makePrivatContactAccess();
        $fakePrivatContactAccess = $this->fakePrivatContactAccessData();
        $updatedPrivatContactAccess = $this->privatContactAccessRepo->update($fakePrivatContactAccess, $privatContactAccess->id);
        $this->assertModelData($fakePrivatContactAccess, $updatedPrivatContactAccess->toArray());
        $dbPrivatContactAccess = $this->privatContactAccessRepo->find($privatContactAccess->id);
        $this->assertModelData($fakePrivatContactAccess, $dbPrivatContactAccess->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePrivatContactAccess()
    {
        $privatContactAccess = $this->makePrivatContactAccess();
        $resp = $this->privatContactAccessRepo->delete($privatContactAccess->id);
        $this->assertTrue($resp);
        $this->assertNull(PrivatContactAccess::find($privatContactAccess->id), 'PrivatContactAccess should not exist in DB');
    }
}
