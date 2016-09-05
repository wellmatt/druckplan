<?php

use Faker\Factory as Faker;
use App\Models\ArticleQualifiedUser;
use App\Repositories\ArticleQualifiedUserRepository;

trait MakeArticleQualifiedUserTrait
{
    /**
     * Create fake instance of ArticleQualifiedUser and save it in database
     *
     * @param array $articleQualifiedUserFields
     * @return ArticleQualifiedUser
     */
    public function makeArticleQualifiedUser($articleQualifiedUserFields = [])
    {
        /** @var ArticleQualifiedUserRepository $articleQualifiedUserRepo */
        $articleQualifiedUserRepo = App::make(ArticleQualifiedUserRepository::class);
        $theme = $this->fakeArticleQualifiedUserData($articleQualifiedUserFields);
        return $articleQualifiedUserRepo->create($theme);
    }

    /**
     * Get fake instance of ArticleQualifiedUser
     *
     * @param array $articleQualifiedUserFields
     * @return ArticleQualifiedUser
     */
    public function fakeArticleQualifiedUser($articleQualifiedUserFields = [])
    {
        return new ArticleQualifiedUser($this->fakeArticleQualifiedUserData($articleQualifiedUserFields));
    }

    /**
     * Get fake data of ArticleQualifiedUser
     *
     * @param array $postFields
     * @return array
     */
    public function fakeArticleQualifiedUserData($articleQualifiedUserFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'article' => $fake->randomDigitNotNull,
            'user' => $fake->randomDigitNotNull
        ], $articleQualifiedUserFields);
    }
}
