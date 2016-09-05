<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AttachmentApiTest extends TestCase
{
    use MakeAttachmentTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAttachment()
    {
        $attachment = $this->fakeAttachmentData();
        $this->json('POST', '/api/v1/attachments', $attachment);

        $this->assertApiResponse($attachment);
    }

    /**
     * @test
     */
    public function testReadAttachment()
    {
        $attachment = $this->makeAttachment();
        $this->json('GET', '/api/v1/attachments/'.$attachment->id);

        $this->assertApiResponse($attachment->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAttachment()
    {
        $attachment = $this->makeAttachment();
        $editedAttachment = $this->fakeAttachmentData();

        $this->json('PUT', '/api/v1/attachments/'.$attachment->id, $editedAttachment);

        $this->assertApiResponse($editedAttachment);
    }

    /**
     * @test
     */
    public function testDeleteAttachment()
    {
        $attachment = $this->makeAttachment();
        $this->json('DELETE', '/api/v1/attachments/'.$attachment->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/attachments/'.$attachment->id);

        $this->assertResponseStatus(404);
    }
}
