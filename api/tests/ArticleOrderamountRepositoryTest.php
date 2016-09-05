<?php

use App\Models\ArticleOrderamount;
use App\Repositories\ArticleOrderamountRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArticleOrderamountRepositoryTest extends TestCase
{
    use MakeArticleOrderamountTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ArticleOrderamountRepository
     */
    protected $articleOrderamountRepo;

    public function setUp()
    {
        parent::setUp();
        $this->articleOrderamountRepo = App::make(ArticleOrderamountRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateArticleOrderamount()
    {
        $articleOrderamount = $this->fakeArticleOrderamountData();
        $createdArticleOrderamount = $this->articleOrderamountRepo->create($articleOrderamount);
        $createdArticleOrderamount = $createdArticleOrderamount->toArray();
        $this->assertArrayHasKey('id', $createdArticleOrderamount);
        $this->assertNotNull($createdArticleOrderamount['id'], 'Created ArticleOrderamount must have id specified');
        $this->assertNotNull(ArticleOrderamount::find($createdArticleOrderamount['id']), 'ArticleOrderamount with given id must be in DB');
        $this->assertModelData($articleOrderamount, $createdArticleOrderamount);
    }

    /**
     * @test read
     */
    public function testReadArticleOrderamount()
    {
        $articleOrderamount = $this->makeArticleOrderamount();
        $dbArticleOrderamount = $this->articleOrderamountRepo->find($articleOrderamount->id);
        $dbArticleOrderamount = $dbArticleOrderamount->toArray();
        $this->assertModelData($articleOrderamount->toArray(), $dbArticleOrderamount);
    }

    /**
     * @test update
     */
    public function testUpdateArticleOrderamount()
    {
        $articleOrderamount = $this->makeArticleOrderamount();
        $fakeArticleOrderamount = $this->fakeArticleOrderamountData();
        $updatedArticleOrderamount = $this->articleOrderamountRepo->update($fakeArticleOrderamount, $articleOrderamount->id);
        $this->assertModelData($fakeArticleOrderamount, $updatedArticleOrderamount->toArray());
        $dbArticleOrderamount = $this->articleOrderamountRepo->find($articleOrderamount->id);
        $this->assertModelData($fakeArticleOrderamount, $dbArticleOrderamount->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteArticleOrderamount()
    {
        $articleOrderamount = $this->makeArticleOrderamount();
        $resp = $this->articleOrderamountRepo->delete($articleOrderamount->id);
        $this->assertTrue($resp);
        $this->assertNull(ArticleOrderamount::find($articleOrderamount->id), 'ArticleOrderamount should not exist in DB');
    }
}
