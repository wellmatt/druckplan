<?php

use Faker\Factory as Faker;
use App\Models\ArticlePricescale;
use App\Repositories\ArticlePricescaleRepository;

trait MakeArticlePricescaleTrait
{
    /**
     * Create fake instance of ArticlePricescale and save it in database
     *
     * @param array $articlePricescaleFields
     * @return ArticlePricescale
     */
    public function makeArticlePricescale($articlePricescaleFields = [])
    {
        /** @var ArticlePricescaleRepository $articlePricescaleRepo */
        $articlePricescaleRepo = App::make(ArticlePricescaleRepository::class);
        $theme = $this->fakeArticlePricescaleData($articlePricescaleFields);
        return $articlePricescaleRepo->create($theme);
    }

    /**
     * Get fake instance of ArticlePricescale
     *
     * @param array $articlePricescaleFields
     * @return ArticlePricescale
     */
    public function fakeArticlePricescale($articlePricescaleFields = [])
    {
        return new ArticlePricescale($this->fakeArticlePricescaleData($articlePricescaleFields));
    }

    /**
     * Get fake data of ArticlePricescale
     *
     * @param array $postFields
     * @return array
     */
    public function fakeArticlePricescaleData($articlePricescaleFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'article' => $fake->randomDigitNotNull,
            'type' => $fake->word,
            'min' => $fake->randomDigitNotNull,
            'max' => $fake->randomDigitNotNull,
            'price' => $fake->randomDigitNotNull,
            'supplier' => $fake->randomDigitNotNull,
            'artnum' => $fake->word
        ], $articlePricescaleFields);
    }
}
