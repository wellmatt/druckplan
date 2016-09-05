<?php

use App\Models\CollectiveinvoiceOrderposition;
use App\Repositories\CollectiveinvoiceOrderpositionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CollectiveinvoiceOrderpositionRepositoryTest extends TestCase
{
    use MakeCollectiveinvoiceOrderpositionTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CollectiveinvoiceOrderpositionRepository
     */
    protected $collectiveinvoiceOrderpositionRepo;

    public function setUp()
    {
        parent::setUp();
        $this->collectiveinvoiceOrderpositionRepo = App::make(CollectiveinvoiceOrderpositionRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCollectiveinvoiceOrderposition()
    {
        $collectiveinvoiceOrderposition = $this->fakeCollectiveinvoiceOrderpositionData();
        $createdCollectiveinvoiceOrderposition = $this->collectiveinvoiceOrderpositionRepo->create($collectiveinvoiceOrderposition);
        $createdCollectiveinvoiceOrderposition = $createdCollectiveinvoiceOrderposition->toArray();
        $this->assertArrayHasKey('id', $createdCollectiveinvoiceOrderposition);
        $this->assertNotNull($createdCollectiveinvoiceOrderposition['id'], 'Created CollectiveinvoiceOrderposition must have id specified');
        $this->assertNotNull(CollectiveinvoiceOrderposition::find($createdCollectiveinvoiceOrderposition['id']), 'CollectiveinvoiceOrderposition with given id must be in DB');
        $this->assertModelData($collectiveinvoiceOrderposition, $createdCollectiveinvoiceOrderposition);
    }

    /**
     * @test read
     */
    public function testReadCollectiveinvoiceOrderposition()
    {
        $collectiveinvoiceOrderposition = $this->makeCollectiveinvoiceOrderposition();
        $dbCollectiveinvoiceOrderposition = $this->collectiveinvoiceOrderpositionRepo->find($collectiveinvoiceOrderposition->id);
        $dbCollectiveinvoiceOrderposition = $dbCollectiveinvoiceOrderposition->toArray();
        $this->assertModelData($collectiveinvoiceOrderposition->toArray(), $dbCollectiveinvoiceOrderposition);
    }

    /**
     * @test update
     */
    public function testUpdateCollectiveinvoiceOrderposition()
    {
        $collectiveinvoiceOrderposition = $this->makeCollectiveinvoiceOrderposition();
        $fakeCollectiveinvoiceOrderposition = $this->fakeCollectiveinvoiceOrderpositionData();
        $updatedCollectiveinvoiceOrderposition = $this->collectiveinvoiceOrderpositionRepo->update($fakeCollectiveinvoiceOrderposition, $collectiveinvoiceOrderposition->id);
        $this->assertModelData($fakeCollectiveinvoiceOrderposition, $updatedCollectiveinvoiceOrderposition->toArray());
        $dbCollectiveinvoiceOrderposition = $this->collectiveinvoiceOrderpositionRepo->find($collectiveinvoiceOrderposition->id);
        $this->assertModelData($fakeCollectiveinvoiceOrderposition, $dbCollectiveinvoiceOrderposition->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCollectiveinvoiceOrderposition()
    {
        $collectiveinvoiceOrderposition = $this->makeCollectiveinvoiceOrderposition();
        $resp = $this->collectiveinvoiceOrderpositionRepo->delete($collectiveinvoiceOrderposition->id);
        $this->assertTrue($resp);
        $this->assertNull(CollectiveinvoiceOrderposition::find($collectiveinvoiceOrderposition->id), 'CollectiveinvoiceOrderposition should not exist in DB');
    }
}
