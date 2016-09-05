<?php

use Faker\Factory as Faker;
use App\Models\ProductPaper;
use App\Repositories\ProductPaperRepository;

trait MakeProductPaperTrait
{
    /**
     * Create fake instance of ProductPaper and save it in database
     *
     * @param array $productPaperFields
     * @return ProductPaper
     */
    public function makeProductPaper($productPaperFields = [])
    {
        /** @var ProductPaperRepository $productPaperRepo */
        $productPaperRepo = App::make(ProductPaperRepository::class);
        $theme = $this->fakeProductPaperData($productPaperFields);
        return $productPaperRepo->create($theme);
    }

    /**
     * Get fake instance of ProductPaper
     *
     * @param array $productPaperFields
     * @return ProductPaper
     */
    public function fakeProductPaper($productPaperFields = [])
    {
        return new ProductPaper($this->fakeProductPaperData($productPaperFields));
    }

    /**
     * Get fake data of ProductPaper
     *
     * @param array $postFields
     * @return array
     */
    public function fakeProductPaperData($productPaperFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'product_id' => $fake->randomDigitNotNull,
            'paper_id' => $fake->randomDigitNotNull,
            'weight' => $fake->word,
            'part' => $fake->word
        ], $productPaperFields);
    }
}
