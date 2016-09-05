<?php

use App\Models\Formats;
use App\Repositories\FormatsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FormatsRepositoryTest extends TestCase
{
    use MakeFormatsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FormatsRepository
     */
    protected $formatsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->formatsRepo = App::make(FormatsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFormats()
    {
        $formats = $this->fakeFormatsData();
        $createdFormats = $this->formatsRepo->create($formats);
        $createdFormats = $createdFormats->toArray();
        $this->assertArrayHasKey('id', $createdFormats);
        $this->assertNotNull($createdFormats['id'], 'Created Formats must have id specified');
        $this->assertNotNull(Formats::find($createdFormats['id']), 'Formats with given id must be in DB');
        $this->assertModelData($formats, $createdFormats);
    }

    /**
     * @test read
     */
    public function testReadFormats()
    {
        $formats = $this->makeFormats();
        $dbFormats = $this->formatsRepo->find($formats->id);
        $dbFormats = $dbFormats->toArray();
        $this->assertModelData($formats->toArray(), $dbFormats);
    }

    /**
     * @test update
     */
    public function testUpdateFormats()
    {
        $formats = $this->makeFormats();
        $fakeFormats = $this->fakeFormatsData();
        $updatedFormats = $this->formatsRepo->update($fakeFormats, $formats->id);
        $this->assertModelData($fakeFormats, $updatedFormats->toArray());
        $dbFormats = $this->formatsRepo->find($formats->id);
        $this->assertModelData($fakeFormats, $dbFormats->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFormats()
    {
        $formats = $this->makeFormats();
        $resp = $this->formatsRepo->delete($formats->id);
        $this->assertTrue($resp);
        $this->assertNull(Formats::find($formats->id), 'Formats should not exist in DB');
    }
}
