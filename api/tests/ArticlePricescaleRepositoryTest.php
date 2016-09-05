<?php

use App\Models\ArticlePricescale;
use App\Repositories\ArticlePricescaleRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArticlePricescaleRepositoryTest extends TestCase
{
    use MakeArticlePricescaleTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ArticlePricescaleRepository
     */
    protected $articlePricescaleRepo;

    public function setUp()
    {
        parent::setUp();
        $this->articlePricescaleRepo = App::make(ArticlePricescaleRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateArticlePricescale()
    {
        $articlePricescale = $this->fakeArticlePricescaleData();
        $createdArticlePricescale = $this->articlePricescaleRepo->create($articlePricescale);
        $createdArticlePricescale = $createdArticlePricescale->toArray();
        $this->assertArrayHasKey('id', $createdArticlePricescale);
        $this->assertNotNull($createdArticlePricescale['id'], 'Created ArticlePricescale must have id specified');
        $this->assertNotNull(ArticlePricescale::find($createdArticlePricescale['id']), 'ArticlePricescale with given id must be in DB');
        $this->assertModelData($articlePricescale, $createdArticlePricescale);
    }

    /**
     * @test read
     */
    public function testReadArticlePricescale()
    {
        $articlePricescale = $this->makeArticlePricescale();
        $dbArticlePricescale = $this->articlePricescaleRepo->find($articlePricescale->id);
        $dbArticlePricescale = $dbArticlePricescale->toArray();
        $this->assertModelData($articlePricescale->toArray(), $dbArticlePricescale);
    }

    /**
     * @test update
     */
    public function testUpdateArticlePricescale()
    {
        $articlePricescale = $this->makeArticlePricescale();
        $fakeArticlePricescale = $this->fakeArticlePricescaleData();
        $updatedArticlePricescale = $this->articlePricescaleRepo->update($fakeArticlePricescale, $articlePricescale->id);
        $this->assertModelData($fakeArticlePricescale, $updatedArticlePricescale->toArray());
        $dbArticlePricescale = $this->articlePricescaleRepo->find($articlePricescale->id);
        $this->assertModelData($fakeArticlePricescale, $dbArticlePricescale->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteArticlePricescale()
    {
        $articlePricescale = $this->makeArticlePricescale();
        $resp = $this->articlePricescaleRepo->delete($articlePricescale->id);
        $this->assertTrue($resp);
        $this->assertNull(ArticlePricescale::find($articlePricescale->id), 'ArticlePricescale should not exist in DB');
    }
}
