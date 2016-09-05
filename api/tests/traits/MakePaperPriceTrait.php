<?php

use Faker\Factory as Faker;
use App\Models\PaperPrice;
use App\Repositories\PaperPriceRepository;

trait MakePaperPriceTrait
{
    /**
     * Create fake instance of PaperPrice and save it in database
     *
     * @param array $paperPriceFields
     * @return PaperPrice
     */
    public function makePaperPrice($paperPriceFields = [])
    {
        /** @var PaperPriceRepository $paperPriceRepo */
        $paperPriceRepo = App::make(PaperPriceRepository::class);
        $theme = $this->fakePaperPriceData($paperPriceFields);
        return $paperPriceRepo->create($theme);
    }

    /**
     * Get fake instance of PaperPrice
     *
     * @param array $paperPriceFields
     * @return PaperPrice
     */
    public function fakePaperPrice($paperPriceFields = [])
    {
        return new PaperPrice($this->fakePaperPriceData($paperPriceFields));
    }

    /**
     * Get fake data of PaperPrice
     *
     * @param array $postFields
     * @return array
     */
    public function fakePaperPriceData($paperPriceFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'paper_id' => $fake->randomDigitNotNull,
            'weight_from' => $fake->randomDigitNotNull,
            'weight_to' => $fake->randomDigitNotNull,
            'size_width' => $fake->randomDigitNotNull,
            'size_height' => $fake->randomDigitNotNull,
            'quantity_from' => $fake->randomDigitNotNull,
            'price' => $fake->randomDigitNotNull,
            'weight' => $fake->randomDigitNotNull
        ], $paperPriceFields);
    }
}
