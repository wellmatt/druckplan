<?php

use Faker\Factory as Faker;
use App\Models\ProductFormat;
use App\Repositories\ProductFormatRepository;

trait MakeProductFormatTrait
{
    /**
     * Create fake instance of ProductFormat and save it in database
     *
     * @param array $productFormatFields
     * @return ProductFormat
     */
    public function makeProductFormat($productFormatFields = [])
    {
        /** @var ProductFormatRepository $productFormatRepo */
        $productFormatRepo = App::make(ProductFormatRepository::class);
        $theme = $this->fakeProductFormatData($productFormatFields);
        return $productFormatRepo->create($theme);
    }

    /**
     * Get fake instance of ProductFormat
     *
     * @param array $productFormatFields
     * @return ProductFormat
     */
    public function fakeProductFormat($productFormatFields = [])
    {
        return new ProductFormat($this->fakeProductFormatData($productFormatFields));
    }

    /**
     * Get fake data of ProductFormat
     *
     * @param array $postFields
     * @return array
     */
    public function fakeProductFormatData($productFormatFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'product_id' => $fake->randomDigitNotNull,
            'format_id' => $fake->randomDigitNotNull
        ], $productFormatFields);
    }
}
