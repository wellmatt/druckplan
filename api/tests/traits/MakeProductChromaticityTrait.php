<?php

use Faker\Factory as Faker;
use App\Models\ProductChromaticity;
use App\Repositories\ProductChromaticityRepository;

trait MakeProductChromaticityTrait
{
    /**
     * Create fake instance of ProductChromaticity and save it in database
     *
     * @param array $productChromaticityFields
     * @return ProductChromaticity
     */
    public function makeProductChromaticity($productChromaticityFields = [])
    {
        /** @var ProductChromaticityRepository $productChromaticityRepo */
        $productChromaticityRepo = App::make(ProductChromaticityRepository::class);
        $theme = $this->fakeProductChromaticityData($productChromaticityFields);
        return $productChromaticityRepo->create($theme);
    }

    /**
     * Get fake instance of ProductChromaticity
     *
     * @param array $productChromaticityFields
     * @return ProductChromaticity
     */
    public function fakeProductChromaticity($productChromaticityFields = [])
    {
        return new ProductChromaticity($this->fakeProductChromaticityData($productChromaticityFields));
    }

    /**
     * Get fake data of ProductChromaticity
     *
     * @param array $postFields
     * @return array
     */
    public function fakeProductChromaticityData($productChromaticityFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'product_id' => $fake->randomDigitNotNull,
            'chromaticity_id' => $fake->randomDigitNotNull
        ], $productChromaticityFields);
    }
}
