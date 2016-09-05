<?php

use Faker\Factory as Faker;
use App\Models\Article;
use App\Repositories\ArticleRepository;

trait MakeArticleTrait
{
    /**
     * Create fake instance of Article and save it in database
     *
     * @param array $articleFields
     * @return Article
     */
    public function makeArticle($articleFields = [])
    {
        /** @var ArticleRepository $articleRepo */
        $articleRepo = App::make(ArticleRepository::class);
        $theme = $this->fakeArticleData($articleFields);
        return $articleRepo->create($theme);
    }

    /**
     * Get fake instance of Article
     *
     * @param array $articleFields
     * @return Article
     */
    public function fakeArticle($articleFields = [])
    {
        return new Article($this->fakeArticleData($articleFields));
    }

    /**
     * Get fake data of Article
     *
     * @param array $postFields
     * @return array
     */
    public function fakeArticleData($articleFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'status' => $fake->randomDigitNotNull,
            'title' => $fake->word,
            'description' => $fake->text,
            'number' => $fake->word,
            'tradegroup' => $fake->randomDigitNotNull,
            'shoprel' => $fake->randomDigitNotNull,
            'crtuser' => $fake->randomDigitNotNull,
            'crtdate' => $fake->randomDigitNotNull,
            'uptuser' => $fake->randomDigitNotNull,
            'uptdate' => $fake->randomDigitNotNull,
            'picture' => $fake->word,
            'tax' => $fake->randomDigitNotNull,
            'minorder' => $fake->randomDigitNotNull,
            'maxorder' => $fake->randomDigitNotNull,
            'orderunit' => $fake->randomDigitNotNull,
            'orderunitweight' => $fake->randomDigitNotNull,
            'shop_customer_rel' => $fake->randomDigitNotNull,
            'shop_customer_id' => $fake->randomDigitNotNull,
            'isworkhourart' => $fake->word,
            'show_shop_price' => $fake->word,
            'shop_needs_upload' => $fake->word,
            'matchcode' => $fake->word,
            'orderid' => $fake->randomDigitNotNull,
            'usesstorage' => $fake->word
        ], $articleFields);
    }
}
