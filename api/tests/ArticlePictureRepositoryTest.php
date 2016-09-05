<?php

use App\Models\ArticlePicture;
use App\Repositories\ArticlePictureRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArticlePictureRepositoryTest extends TestCase
{
    use MakeArticlePictureTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ArticlePictureRepository
     */
    protected $articlePictureRepo;

    public function setUp()
    {
        parent::setUp();
        $this->articlePictureRepo = App::make(ArticlePictureRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateArticlePicture()
    {
        $articlePicture = $this->fakeArticlePictureData();
        $createdArticlePicture = $this->articlePictureRepo->create($articlePicture);
        $createdArticlePicture = $createdArticlePicture->toArray();
        $this->assertArrayHasKey('id', $createdArticlePicture);
        $this->assertNotNull($createdArticlePicture['id'], 'Created ArticlePicture must have id specified');
        $this->assertNotNull(ArticlePicture::find($createdArticlePicture['id']), 'ArticlePicture with given id must be in DB');
        $this->assertModelData($articlePicture, $createdArticlePicture);
    }

    /**
     * @test read
     */
    public function testReadArticlePicture()
    {
        $articlePicture = $this->makeArticlePicture();
        $dbArticlePicture = $this->articlePictureRepo->find($articlePicture->id);
        $dbArticlePicture = $dbArticlePicture->toArray();
        $this->assertModelData($articlePicture->toArray(), $dbArticlePicture);
    }

    /**
     * @test update
     */
    public function testUpdateArticlePicture()
    {
        $articlePicture = $this->makeArticlePicture();
        $fakeArticlePicture = $this->fakeArticlePictureData();
        $updatedArticlePicture = $this->articlePictureRepo->update($fakeArticlePicture, $articlePicture->id);
        $this->assertModelData($fakeArticlePicture, $updatedArticlePicture->toArray());
        $dbArticlePicture = $this->articlePictureRepo->find($articlePicture->id);
        $this->assertModelData($fakeArticlePicture, $dbArticlePicture->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteArticlePicture()
    {
        $articlePicture = $this->makeArticlePicture();
        $resp = $this->articlePictureRepo->delete($articlePicture->id);
        $this->assertTrue($resp);
        $this->assertNull(ArticlePicture::find($articlePicture->id), 'ArticlePicture should not exist in DB');
    }
}
