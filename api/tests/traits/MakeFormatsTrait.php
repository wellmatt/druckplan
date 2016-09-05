<?php

use Faker\Factory as Faker;
use App\Models\Formats;
use App\Repositories\FormatsRepository;

trait MakeFormatsTrait
{
    /**
     * Create fake instance of Formats and save it in database
     *
     * @param array $formatsFields
     * @return Formats
     */
    public function makeFormats($formatsFields = [])
    {
        /** @var FormatsRepository $formatsRepo */
        $formatsRepo = App::make(FormatsRepository::class);
        $theme = $this->fakeFormatsData($formatsFields);
        return $formatsRepo->create($theme);
    }

    /**
     * Get fake instance of Formats
     *
     * @param array $formatsFields
     * @return Formats
     */
    public function fakeFormats($formatsFields = [])
    {
        return new Formats($this->fakeFormatsData($formatsFields));
    }

    /**
     * Get fake data of Formats
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFormatsData($formatsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'id' => $fake->randomDigitNotNull,
            'name' => $fake->word,
            'width' => $fake->randomDigitNotNull,
            'height' => $fake->randomDigitNotNull
        ], $formatsFields);
    }
}
