<?php

use Faker\Factory as Faker;
use App\Models\Product;
use App\Repositories\ProductRepository;

trait MakeProductTrait
{
    /**
     * Create fake instance of Product and save it in database
     *
     * @param array $productFields
     * @return Product
     */
    public function makeProduct($productFields = [])
    {
        /** @var ProductRepository $productRepo */
        $productRepo = App::make(ProductRepository::class);
        $theme = $this->fakeProductData($productFields);
        return $productRepo->create($theme);
    }

    /**
     * Get fake instance of Product
     *
     * @param array $productFields
     * @return Product
     */
    public function fakeProduct($productFields = [])
    {
        return new Product($this->fakeProductData($productFields));
    }

    /**
     * Get fake data of Product
     *
     * @param array $postFields
     * @return array
     */
    public function fakeProductData($productFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'state' => $fake->word,
            'name' => $fake->word,
            'description' => $fake->text,
            'picture' => $fake->word,
            'pages_from' => $fake->word,
            'pages_to' => $fake->word,
            'pages_step' => $fake->word,
            'has_content' => $fake->word,
            'has_addcontent' => $fake->word,
            'has_envelope' => $fake->word,
            'factor_width' => $fake->randomDigitNotNull,
            'factor_height' => $fake->randomDigitNotNull,
            'taxes' => $fake->randomDigitNotNull,
            'grant_paper' => $fake->randomDigitNotNull,
            'type' => $fake->word,
            'text_offer' => $fake->text,
            'text_offerconfirm' => $fake->text,
            'text_invoice' => $fake->text,
            'text_processing' => $fake->text,
            'shop_rel' => $fake->randomDigitNotNull,
            'tradegroup' => $fake->randomDigitNotNull,
            'is_individual' => $fake->word,
            'has_addcontent2' => $fake->word,
            'has_addcontent3' => $fake->word,
            'load_dummydata' => $fake->word,
            'singleplateset' => $fake->word,
            'blockplateset' => $fake->word
        ], $productFields);
    }
}
