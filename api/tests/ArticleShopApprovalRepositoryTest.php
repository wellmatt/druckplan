<?php

use App\Models\ArticleShopApproval;
use App\Repositories\ArticleShopApprovalRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArticleShopApprovalRepositoryTest extends TestCase
{
    use MakeArticleShopApprovalTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ArticleShopApprovalRepository
     */
    protected $articleShopApprovalRepo;

    public function setUp()
    {
        parent::setUp();
        $this->articleShopApprovalRepo = App::make(ArticleShopApprovalRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateArticleShopApproval()
    {
        $articleShopApproval = $this->fakeArticleShopApprovalData();
        $createdArticleShopApproval = $this->articleShopApprovalRepo->create($articleShopApproval);
        $createdArticleShopApproval = $createdArticleShopApproval->toArray();
        $this->assertArrayHasKey('id', $createdArticleShopApproval);
        $this->assertNotNull($createdArticleShopApproval['id'], 'Created ArticleShopApproval must have id specified');
        $this->assertNotNull(ArticleShopApproval::find($createdArticleShopApproval['id']), 'ArticleShopApproval with given id must be in DB');
        $this->assertModelData($articleShopApproval, $createdArticleShopApproval);
    }

    /**
     * @test read
     */
    public function testReadArticleShopApproval()
    {
        $articleShopApproval = $this->makeArticleShopApproval();
        $dbArticleShopApproval = $this->articleShopApprovalRepo->find($articleShopApproval->id);
        $dbArticleShopApproval = $dbArticleShopApproval->toArray();
        $this->assertModelData($articleShopApproval->toArray(), $dbArticleShopApproval);
    }

    /**
     * @test update
     */
    public function testUpdateArticleShopApproval()
    {
        $articleShopApproval = $this->makeArticleShopApproval();
        $fakeArticleShopApproval = $this->fakeArticleShopApprovalData();
        $updatedArticleShopApproval = $this->articleShopApprovalRepo->update($fakeArticleShopApproval, $articleShopApproval->id);
        $this->assertModelData($fakeArticleShopApproval, $updatedArticleShopApproval->toArray());
        $dbArticleShopApproval = $this->articleShopApprovalRepo->find($articleShopApproval->id);
        $this->assertModelData($fakeArticleShopApproval, $dbArticleShopApproval->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteArticleShopApproval()
    {
        $articleShopApproval = $this->makeArticleShopApproval();
        $resp = $this->articleShopApprovalRepo->delete($articleShopApproval->id);
        $this->assertTrue($resp);
        $this->assertNull(ArticleShopApproval::find($articleShopApproval->id), 'ArticleShopApproval should not exist in DB');
    }
}
