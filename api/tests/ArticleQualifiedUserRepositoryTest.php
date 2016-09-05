<?php

use App\Models\ArticleQualifiedUser;
use App\Repositories\ArticleQualifiedUserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArticleQualifiedUserRepositoryTest extends TestCase
{
    use MakeArticleQualifiedUserTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ArticleQualifiedUserRepository
     */
    protected $articleQualifiedUserRepo;

    public function setUp()
    {
        parent::setUp();
        $this->articleQualifiedUserRepo = App::make(ArticleQualifiedUserRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateArticleQualifiedUser()
    {
        $articleQualifiedUser = $this->fakeArticleQualifiedUserData();
        $createdArticleQualifiedUser = $this->articleQualifiedUserRepo->create($articleQualifiedUser);
        $createdArticleQualifiedUser = $createdArticleQualifiedUser->toArray();
        $this->assertArrayHasKey('id', $createdArticleQualifiedUser);
        $this->assertNotNull($createdArticleQualifiedUser['id'], 'Created ArticleQualifiedUser must have id specified');
        $this->assertNotNull(ArticleQualifiedUser::find($createdArticleQualifiedUser['id']), 'ArticleQualifiedUser with given id must be in DB');
        $this->assertModelData($articleQualifiedUser, $createdArticleQualifiedUser);
    }

    /**
     * @test read
     */
    public function testReadArticleQualifiedUser()
    {
        $articleQualifiedUser = $this->makeArticleQualifiedUser();
        $dbArticleQualifiedUser = $this->articleQualifiedUserRepo->find($articleQualifiedUser->id);
        $dbArticleQualifiedUser = $dbArticleQualifiedUser->toArray();
        $this->assertModelData($articleQualifiedUser->toArray(), $dbArticleQualifiedUser);
    }

    /**
     * @test update
     */
    public function testUpdateArticleQualifiedUser()
    {
        $articleQualifiedUser = $this->makeArticleQualifiedUser();
        $fakeArticleQualifiedUser = $this->fakeArticleQualifiedUserData();
        $updatedArticleQualifiedUser = $this->articleQualifiedUserRepo->update($fakeArticleQualifiedUser, $articleQualifiedUser->id);
        $this->assertModelData($fakeArticleQualifiedUser, $updatedArticleQualifiedUser->toArray());
        $dbArticleQualifiedUser = $this->articleQualifiedUserRepo->find($articleQualifiedUser->id);
        $this->assertModelData($fakeArticleQualifiedUser, $dbArticleQualifiedUser->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteArticleQualifiedUser()
    {
        $articleQualifiedUser = $this->makeArticleQualifiedUser();
        $resp = $this->articleQualifiedUserRepo->delete($articleQualifiedUser->id);
        $this->assertTrue($resp);
        $this->assertNull(ArticleQualifiedUser::find($articleQualifiedUser->id), 'ArticleQualifiedUser should not exist in DB');
    }
}
