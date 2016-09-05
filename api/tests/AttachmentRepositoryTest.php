<?php

use App\Models\Attachment;
use App\Repositories\AttachmentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AttachmentRepositoryTest extends TestCase
{
    use MakeAttachmentTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AttachmentRepository
     */
    protected $attachmentRepo;

    public function setUp()
    {
        parent::setUp();
        $this->attachmentRepo = App::make(AttachmentRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAttachment()
    {
        $attachment = $this->fakeAttachmentData();
        $createdAttachment = $this->attachmentRepo->create($attachment);
        $createdAttachment = $createdAttachment->toArray();
        $this->assertArrayHasKey('id', $createdAttachment);
        $this->assertNotNull($createdAttachment['id'], 'Created Attachment must have id specified');
        $this->assertNotNull(Attachment::find($createdAttachment['id']), 'Attachment with given id must be in DB');
        $this->assertModelData($attachment, $createdAttachment);
    }

    /**
     * @test read
     */
    public function testReadAttachment()
    {
        $attachment = $this->makeAttachment();
        $dbAttachment = $this->attachmentRepo->find($attachment->id);
        $dbAttachment = $dbAttachment->toArray();
        $this->assertModelData($attachment->toArray(), $dbAttachment);
    }

    /**
     * @test update
     */
    public function testUpdateAttachment()
    {
        $attachment = $this->makeAttachment();
        $fakeAttachment = $this->fakeAttachmentData();
        $updatedAttachment = $this->attachmentRepo->update($fakeAttachment, $attachment->id);
        $this->assertModelData($fakeAttachment, $updatedAttachment->toArray());
        $dbAttachment = $this->attachmentRepo->find($attachment->id);
        $this->assertModelData($fakeAttachment, $dbAttachment->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAttachment()
    {
        $attachment = $this->makeAttachment();
        $resp = $this->attachmentRepo->delete($attachment->id);
        $this->assertTrue($resp);
        $this->assertNull(Attachment::find($attachment->id), 'Attachment should not exist in DB');
    }
}
