<?php

use Faker\Factory as Faker;
use App\Models\ArticlePicture;
use App\Repositories\ArticlePictureRepository;

trait MakeArticlePictureTrait
{
    /**
     * Create fake instance of ArticlePicture and save it in database
     *
     * @param array $articlePictureFields
     * @return ArticlePicture
     */
    public function makeArticlePicture($articlePictureFields = [])
    {
        /** @var ArticlePictureRepository $articlePictureRepo */
        $articlePictureRepo = App::make(ArticlePictureRepository::class);
        $theme = $this->fakeArticlePictureData($articlePictureFields);
        return $articlePictureRepo->create($theme);
    }

    /**
     * Get fake instance of ArticlePicture
     *
     * @param array $articlePictureFields
     * @return ArticlePicture
     */
    public function fakeArticlePicture($articlePictureFields = [])
    {
        return new ArticlePicture($this->fakeArticlePictureData($articlePictureFields));
    }

    /**
     * Get fake data of ArticlePicture
     *
     * @param array $postFields
     * @return array
     */
    public function fakeArticlePictureData($articlePictureFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'url' => $fake->word,
            'crtdate' => $fake->randomDigitNotNull,
            'articleid' => $fake->randomDigitNotNull
        ], $articlePictureFields);
    }
}
