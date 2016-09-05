<?php

use Faker\Factory as Faker;
use App\Models\Language;
use App\Repositories\LanguageRepository;

trait MakeLanguageTrait
{
    /**
     * Create fake instance of Language and save it in database
     *
     * @param array $languageFields
     * @return Language
     */
    public function makeLanguage($languageFields = [])
    {
        /** @var LanguageRepository $languageRepo */
        $languageRepo = App::make(LanguageRepository::class);
        $theme = $this->fakeLanguageData($languageFields);
        return $languageRepo->create($theme);
    }

    /**
     * Get fake instance of Language
     *
     * @param array $languageFields
     * @return Language
     */
    public function fakeLanguage($languageFields = [])
    {
        return new Language($this->fakeLanguageData($languageFields));
    }

    /**
     * Get fake data of Language
     *
     * @param array $postFields
     * @return array
     */
    public function fakeLanguageData($languageFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'id' => $fake->randomDigitNotNull,
            'language' => $fake->word,
            'language_int' => $fake->word,
            'language_code' => $fake->word,
            'language_active' => $fake->word
        ], $languageFields);
    }
}
