<?php

namespace App\Repositories;

use App\Models\OrderCalculation;
use InfyOm\Generator\Common\BaseRepository;

class OrderCalculationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'order_id',
        'state',
        'product_format',
        'product_format_width',
        'product_format_height',
        'product_format_width_open',
        'product_format_height_open',
        'product_pages_content',
        'product_pages_addcontent',
        'product_pages_envelope',
        'product_amount',
        'product_sorts',
        'paper_content',
        'paper_content_width',
        'paper_content_height',
        'paper_content_weight',
        'paper_addcontent',
        'paper_addcontent_width',
        'paper_addcontent_height',
        'paper_addcontent_weight',
        'paper_envelope',
        'paper_envelope_width',
        'paper_envelope_height',
        'paper_envelope_weight',
        'product_folding',
        'add_charge',
        'margin',
        'discount',
        'chromaticities_content',
        'chromaticities_addcontent',
        'chromaticities_envelope',
        'envelope_height_open',
        'envelope_width_open',
        'calc_auto_values',
        'calc_debug',
        'paper_content_grant',
        'paper_addcontent_grant',
        'paper_envelope_grant',
        'text_processing',
        'foldscheme_content',
        'foldscheme_addcontent',
        'foldscheme_envelope',
        'crtdat',
        'crtusr',
        'upddat',
        'updusr',
        'product_pages_addcontent2',
        'paper_addcontent2',
        'paper_addcontent2_width',
        'paper_addcontent2_height',
        'paper_addcontent2_weight',
        'chromaticities_addcontent2',
        'paper_addcontent2_grant',
        'foldscheme_addcontent2',
        'product_pages_addcontent3',
        'paper_addcontent3',
        'paper_addcontent3_width',
        'paper_addcontent3_height',
        'paper_addcontent3_weight',
        'chromaticities_addcontent3',
        'paper_addcontent3_grant',
        'foldscheme_addcontent3',
        'cut_content',
        'cut_addcontent',
        'cut_addcontent2',
        'cut_addcontent3',
        'cut_envelope',
        'color_control',
        'cutter_weight',
        'cutter_height',
        'roll_dir',
        'title',
        'format_in_content',
        'format_in_addcontent',
        'format_in_addcontent2',
        'format_in_addcontent3',
        'format_in_envelope',
        'pricesub',
        'pricetotal'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return OrderCalculation::class;
    }
}
