<?php

use Faker\Factory as Faker;
use App\Models\ArticleOrderamount;
use App\Repositories\ArticleOrderamountRepository;

trait MakeArticleOrderamountTrait
{
    /**
     * Create fake instance of ArticleOrderamount and save it in database
     *
     * @param array $articleOrderamountFields
     * @return ArticleOrderamount
     */
    public function makeArticleOrderamount($articleOrderamountFields = [])
    {
        /** @var ArticleOrderamountRepository $articleOrderamountRepo */
        $articleOrderamountRepo = App::make(ArticleOrderamountRepository::class);
        $theme = $this->fakeArticleOrderamountData($articleOrderamountFields);
        return $articleOrderamountRepo->create($theme);
    }

    /**
     * Get fake instance of ArticleOrderamount
     *
     * @param array $articleOrderamountFields
     * @return ArticleOrderamount
     */
    public function fakeArticleOrderamount($articleOrderamountFields = [])
    {
        return new ArticleOrderamount($this->fakeArticleOrderamountData($articleOrderamountFields));
    }

    /**
     * Get fake data of ArticleOrderamount
     *
     * @param array $postFields
     * @return array
     */
    public function fakeArticleOrderamountData($articleOrderamountFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'article_id' => $fake->randomDigitNotNull,
            'amount' => $fake->randomDigitNotNull
        ], $articleOrderamountFields);
    }
}
