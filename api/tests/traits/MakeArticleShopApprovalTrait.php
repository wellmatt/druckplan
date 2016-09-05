<?php

use Faker\Factory as Faker;
use App\Models\ArticleShopApproval;
use App\Repositories\ArticleShopApprovalRepository;

trait MakeArticleShopApprovalTrait
{
    /**
     * Create fake instance of ArticleShopApproval and save it in database
     *
     * @param array $articleShopApprovalFields
     * @return ArticleShopApproval
     */
    public function makeArticleShopApproval($articleShopApprovalFields = [])
    {
        /** @var ArticleShopApprovalRepository $articleShopApprovalRepo */
        $articleShopApprovalRepo = App::make(ArticleShopApprovalRepository::class);
        $theme = $this->fakeArticleShopApprovalData($articleShopApprovalFields);
        return $articleShopApprovalRepo->create($theme);
    }

    /**
     * Get fake instance of ArticleShopApproval
     *
     * @param array $articleShopApprovalFields
     * @return ArticleShopApproval
     */
    public function fakeArticleShopApproval($articleShopApprovalFields = [])
    {
        return new ArticleShopApproval($this->fakeArticleShopApprovalData($articleShopApprovalFields));
    }

    /**
     * Get fake data of ArticleShopApproval
     *
     * @param array $postFields
     * @return array
     */
    public function fakeArticleShopApprovalData($articleShopApprovalFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'article' => $fake->randomDigitNotNull,
            'bc' => $fake->randomDigitNotNull,
            'cp' => $fake->randomDigitNotNull
        ], $articleShopApprovalFields);
    }
}
