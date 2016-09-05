<?php

use App\Models\ArticleTag;
use App\Repositories\ArticleTagRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArticleTagRepositoryTest extends TestCase
{
    use MakeArticleTagTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ArticleTagRepository
     */
    protected $articleTagRepo;

    public function setUp()
    {
        parent::setUp();
        $this->articleTagRepo = App::make(ArticleTagRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateArticleTag()
    {
        $articleTag = $this->fakeArticleTagData();
        $createdArticleTag = $this->articleTagRepo->create($articleTag);
        $createdArticleTag = $createdArticleTag->toArray();
        $this->assertArrayHasKey('id', $createdArticleTag);
        $this->assertNotNull($createdArticleTag['id'], 'Created ArticleTag must have id specified');
        $this->assertNotNull(ArticleTag::find($createdArticleTag['id']), 'ArticleTag with given id must be in DB');
        $this->assertModelData($articleTag, $createdArticleTag);
    }

    /**
     * @test read
     */
    public function testReadArticleTag()
    {
        $articleTag = $this->makeArticleTag();
        $dbArticleTag = $this->articleTagRepo->find($articleTag->id);
        $dbArticleTag = $dbArticleTag->toArray();
        $this->assertModelData($articleTag->toArray(), $dbArticleTag);
    }

    /**
     * @test update
     */
    public function testUpdateArticleTag()
    {
        $articleTag = $this->makeArticleTag();
        $fakeArticleTag = $this->fakeArticleTagData();
        $updatedArticleTag = $this->articleTagRepo->update($fakeArticleTag, $articleTag->id);
        $this->assertModelData($fakeArticleTag, $updatedArticleTag->toArray());
        $dbArticleTag = $this->articleTagRepo->find($articleTag->id);
        $this->assertModelData($fakeArticleTag, $dbArticleTag->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteArticleTag()
    {
        $articleTag = $this->makeArticleTag();
        $resp = $this->articleTagRepo->delete($articleTag->id);
        $this->assertTrue($resp);
        $this->assertNull(ArticleTag::find($articleTag->id), 'ArticleTag should not exist in DB');
    }
}
