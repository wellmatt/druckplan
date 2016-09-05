<?php

use Faker\Factory as Faker;
use App\Models\OrderCalculation;
use App\Repositories\OrderCalculationRepository;

trait MakeOrderCalculationTrait
{
    /**
     * Create fake instance of OrderCalculation and save it in database
     *
     * @param array $orderCalculationFields
     * @return OrderCalculation
     */
    public function makeOrderCalculation($orderCalculationFields = [])
    {
        /** @var OrderCalculationRepository $orderCalculationRepo */
        $orderCalculationRepo = App::make(OrderCalculationRepository::class);
        $theme = $this->fakeOrderCalculationData($orderCalculationFields);
        return $orderCalculationRepo->create($theme);
    }

    /**
     * Get fake instance of OrderCalculation
     *
     * @param array $orderCalculationFields
     * @return OrderCalculation
     */
    public function fakeOrderCalculation($orderCalculationFields = [])
    {
        return new OrderCalculation($this->fakeOrderCalculationData($orderCalculationFields));
    }

    /**
     * Get fake data of OrderCalculation
     *
     * @param array $postFields
     * @return array
     */
    public function fakeOrderCalculationData($orderCalculationFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'order_id' => $fake->randomDigitNotNull,
            'state' => $fake->word,
            'product_format' => $fake->randomDigitNotNull,
            'product_format_width' => $fake->randomDigitNotNull,
            'product_format_height' => $fake->randomDigitNotNull,
            'product_format_width_open' => $fake->randomDigitNotNull,
            'product_format_height_open' => $fake->randomDigitNotNull,
            'product_pages_content' => $fake->randomDigitNotNull,
            'product_pages_addcontent' => $fake->randomDigitNotNull,
            'product_pages_envelope' => $fake->randomDigitNotNull,
            'product_amount' => $fake->randomDigitNotNull,
            'product_sorts' => $fake->randomDigitNotNull,
            'paper_content' => $fake->randomDigitNotNull,
            'paper_content_width' => $fake->randomDigitNotNull,
            'paper_content_height' => $fake->randomDigitNotNull,
            'paper_content_weight' => $fake->randomDigitNotNull,
            'paper_addcontent' => $fake->randomDigitNotNull,
            'paper_addcontent_width' => $fake->randomDigitNotNull,
            'paper_addcontent_height' => $fake->randomDigitNotNull,
            'paper_addcontent_weight' => $fake->randomDigitNotNull,
            'paper_envelope' => $fake->randomDigitNotNull,
            'paper_envelope_width' => $fake->randomDigitNotNull,
            'paper_envelope_height' => $fake->randomDigitNotNull,
            'paper_envelope_weight' => $fake->randomDigitNotNull,
            'product_folding' => $fake->randomDigitNotNull,
            'add_charge' => $fake->randomDigitNotNull,
            'margin' => $fake->randomDigitNotNull,
            'discount' => $fake->randomDigitNotNull,
            'chromaticities_content' => $fake->randomDigitNotNull,
            'chromaticities_addcontent' => $fake->randomDigitNotNull,
            'chromaticities_envelope' => $fake->randomDigitNotNull,
            'envelope_height_open' => $fake->randomDigitNotNull,
            'envelope_width_open' => $fake->randomDigitNotNull,
            'calc_auto_values' => $fake->word,
            'calc_debug' => $fake->word,
            'paper_content_grant' => $fake->randomDigitNotNull,
            'paper_addcontent_grant' => $fake->randomDigitNotNull,
            'paper_envelope_grant' => $fake->randomDigitNotNull,
            'text_processing' => $fake->text,
            'foldscheme_content' => $fake->word,
            'foldscheme_addcontent' => $fake->word,
            'foldscheme_envelope' => $fake->word,
            'crtdat' => $fake->randomDigitNotNull,
            'crtusr' => $fake->randomDigitNotNull,
            'upddat' => $fake->randomDigitNotNull,
            'updusr' => $fake->randomDigitNotNull,
            'product_pages_addcontent2' => $fake->randomDigitNotNull,
            'paper_addcontent2' => $fake->randomDigitNotNull,
            'paper_addcontent2_width' => $fake->randomDigitNotNull,
            'paper_addcontent2_height' => $fake->randomDigitNotNull,
            'paper_addcontent2_weight' => $fake->randomDigitNotNull,
            'chromaticities_addcontent2' => $fake->randomDigitNotNull,
            'paper_addcontent2_grant' => $fake->randomDigitNotNull,
            'foldscheme_addcontent2' => $fake->word,
            'product_pages_addcontent3' => $fake->randomDigitNotNull,
            'paper_addcontent3' => $fake->randomDigitNotNull,
            'paper_addcontent3_width' => $fake->randomDigitNotNull,
            'paper_addcontent3_height' => $fake->randomDigitNotNull,
            'paper_addcontent3_weight' => $fake->randomDigitNotNull,
            'chromaticities_addcontent3' => $fake->randomDigitNotNull,
            'paper_addcontent3_grant' => $fake->randomDigitNotNull,
            'foldscheme_addcontent3' => $fake->word,
            'cut_content' => $fake->randomDigitNotNull,
            'cut_addcontent' => $fake->randomDigitNotNull,
            'cut_addcontent2' => $fake->randomDigitNotNull,
            'cut_addcontent3' => $fake->randomDigitNotNull,
            'cut_envelope' => $fake->randomDigitNotNull,
            'color_control' => $fake->word,
            'cutter_weight' => $fake->randomDigitNotNull,
            'cutter_height' => $fake->randomDigitNotNull,
            'roll_dir' => $fake->randomDigitNotNull,
            'title' => $fake->word,
            'format_in_content' => $fake->word,
            'format_in_addcontent' => $fake->word,
            'format_in_addcontent2' => $fake->word,
            'format_in_addcontent3' => $fake->word,
            'format_in_envelope' => $fake->word,
            'pricesub' => $fake->randomDigitNotNull,
            'pricetotal' => $fake->randomDigitNotNull
        ], $orderCalculationFields);
    }
}
