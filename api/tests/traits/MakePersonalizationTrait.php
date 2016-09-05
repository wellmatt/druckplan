<?php

use Faker\Factory as Faker;
use App\Models\Personalization;
use App\Repositories\PersonalizationRepository;

trait MakePersonalizationTrait
{
    /**
     * Create fake instance of Personalization and save it in database
     *
     * @param array $personalizationFields
     * @return Personalization
     */
    public function makePersonalization($personalizationFields = [])
    {
        /** @var PersonalizationRepository $personalizationRepo */
        $personalizationRepo = App::make(PersonalizationRepository::class);
        $theme = $this->fakePersonalizationData($personalizationFields);
        return $personalizationRepo->create($theme);
    }

    /**
     * Get fake instance of Personalization
     *
     * @param array $personalizationFields
     * @return Personalization
     */
    public function fakePersonalization($personalizationFields = [])
    {
        return new Personalization($this->fakePersonalizationData($personalizationFields));
    }

    /**
     * Get fake data of Personalization
     *
     * @param array $postFields
     * @return array
     */
    public function fakePersonalizationData($personalizationFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'title' => $fake->word,
            'comment' => $fake->text,
            'status' => $fake->word,
            'picture' => $fake->word,
            'article' => $fake->randomDigitNotNull,
            'customer' => $fake->randomDigitNotNull,
            'crtdate' => $fake->randomDigitNotNull,
            'crtuser' => $fake->randomDigitNotNull,
            'uptdate' => $fake->randomDigitNotNull,
            'uptuser' => $fake->randomDigitNotNull,
            'direction' => $fake->randomDigitNotNull,
            'format' => $fake->word,
            'format_width' => $fake->randomDigitNotNull,
            'format_height' => $fake->randomDigitNotNull,
            'type' => $fake->word,
            'picture2' => $fake->word,
            'linebyline' => $fake->word,
            'hidden' => $fake->randomDigitNotNull,
            'anschnitt' => $fake->randomDigitNotNull,
            'preview' => $fake->word
        ], $personalizationFields);
    }
}
