<?php

use Faker\Factory as Faker;
use App\Models\Attachment;
use App\Repositories\AttachmentRepository;

trait MakeAttachmentTrait
{
    /**
     * Create fake instance of Attachment and save it in database
     *
     * @param array $attachmentFields
     * @return Attachment
     */
    public function makeAttachment($attachmentFields = [])
    {
        /** @var AttachmentRepository $attachmentRepo */
        $attachmentRepo = App::make(AttachmentRepository::class);
        $theme = $this->fakeAttachmentData($attachmentFields);
        return $attachmentRepo->create($theme);
    }

    /**
     * Get fake instance of Attachment
     *
     * @param array $attachmentFields
     * @return Attachment
     */
    public function fakeAttachment($attachmentFields = [])
    {
        return new Attachment($this->fakeAttachmentData($attachmentFields));
    }

    /**
     * Get fake data of Attachment
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAttachmentData($attachmentFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'title' => $fake->word,
            'crtdate' => $fake->randomDigitNotNull,
            'crtuser' => $fake->randomDigitNotNull,
            'state' => $fake->word,
            'module' => $fake->word,
            'objectid' => $fake->randomDigitNotNull,
            'filename' => $fake->word,
            'orig_filename' => $fake->word
        ], $attachmentFields);
    }
}
