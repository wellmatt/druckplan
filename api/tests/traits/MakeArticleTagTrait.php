<?php

use Faker\Factory as Faker;
use App\Models\ArticleTag;
use App\Repositories\ArticleTagRepository;

trait MakeArticleTagTrait
{
    /**
     * Create fake instance of ArticleTag and save it in database
     *
     * @param array $articleTagFields
     * @return ArticleTag
     */
    public function makeArticleTag($articleTagFields = [])
    {
        /** @var ArticleTagRepository $articleTagRepo */
        $articleTagRepo = App::make(ArticleTagRepository::class);
        $theme = $this->fakeArticleTagData($articleTagFields);
        return $articleTagRepo->create($theme);
    }

    /**
     * Get fake instance of ArticleTag
     *
     * @param array $articleTagFields
     * @return ArticleTag
     */
    public function fakeArticleTag($articleTagFields = [])
    {
        return new ArticleTag($this->fakeArticleTagData($articleTagFields));
    }

    /**
     * Get fake data of ArticleTag
     *
     * @param array $postFields
     * @return array
     */
    public function fakeArticleTagData($articleTagFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'article' => $fake->randomDigitNotNull,
            'tag' => $fake->word
        ], $articleTagFields);
    }
}
