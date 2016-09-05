<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="OrderCalculation",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="order_id",
 *          description="order_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="product_format",
 *          description="product_format",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="product_format_width",
 *          description="product_format_width",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="product_format_height",
 *          description="product_format_height",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="product_format_width_open",
 *          description="product_format_width_open",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="product_format_height_open",
 *          description="product_format_height_open",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="product_pages_content",
 *          description="product_pages_content",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="product_pages_addcontent",
 *          description="product_pages_addcontent",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="product_pages_envelope",
 *          description="product_pages_envelope",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="product_amount",
 *          description="product_amount",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="product_sorts",
 *          description="product_sorts",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_content",
 *          description="paper_content",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_content_width",
 *          description="paper_content_width",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_content_height",
 *          description="paper_content_height",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_content_weight",
 *          description="paper_content_weight",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_addcontent",
 *          description="paper_addcontent",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_addcontent_width",
 *          description="paper_addcontent_width",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_addcontent_height",
 *          description="paper_addcontent_height",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_addcontent_weight",
 *          description="paper_addcontent_weight",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_envelope",
 *          description="paper_envelope",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_envelope_width",
 *          description="paper_envelope_width",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_envelope_height",
 *          description="paper_envelope_height",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_envelope_weight",
 *          description="paper_envelope_weight",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="product_folding",
 *          description="product_folding",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="add_charge",
 *          description="add_charge",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="margin",
 *          description="margin",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="discount",
 *          description="discount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="chromaticities_content",
 *          description="chromaticities_content",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chromaticities_addcontent",
 *          description="chromaticities_addcontent",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chromaticities_envelope",
 *          description="chromaticities_envelope",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="envelope_height_open",
 *          description="envelope_height_open",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="envelope_width_open",
 *          description="envelope_width_open",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_content_grant",
 *          description="paper_content_grant",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_addcontent_grant",
 *          description="paper_addcontent_grant",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_envelope_grant",
 *          description="paper_envelope_grant",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="text_processing",
 *          description="text_processing",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="foldscheme_content",
 *          description="foldscheme_content",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="foldscheme_addcontent",
 *          description="foldscheme_addcontent",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="foldscheme_envelope",
 *          description="foldscheme_envelope",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="crtdat",
 *          description="crtdat",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="crtusr",
 *          description="crtusr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="upddat",
 *          description="upddat",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updusr",
 *          description="updusr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="product_pages_addcontent2",
 *          description="product_pages_addcontent2",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_addcontent2",
 *          description="paper_addcontent2",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_addcontent2_width",
 *          description="paper_addcontent2_width",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_addcontent2_height",
 *          description="paper_addcontent2_height",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_addcontent2_weight",
 *          description="paper_addcontent2_weight",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chromaticities_addcontent2",
 *          description="chromaticities_addcontent2",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_addcontent2_grant",
 *          description="paper_addcontent2_grant",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="foldscheme_addcontent2",
 *          description="foldscheme_addcontent2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="product_pages_addcontent3",
 *          description="product_pages_addcontent3",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_addcontent3",
 *          description="paper_addcontent3",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_addcontent3_width",
 *          description="paper_addcontent3_width",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_addcontent3_height",
 *          description="paper_addcontent3_height",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_addcontent3_weight",
 *          description="paper_addcontent3_weight",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chromaticities_addcontent3",
 *          description="chromaticities_addcontent3",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_addcontent3_grant",
 *          description="paper_addcontent3_grant",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="foldscheme_addcontent3",
 *          description="foldscheme_addcontent3",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="cut_content",
 *          description="cut_content",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="cut_addcontent",
 *          description="cut_addcontent",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="cut_addcontent2",
 *          description="cut_addcontent2",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="cut_addcontent3",
 *          description="cut_addcontent3",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="cut_envelope",
 *          description="cut_envelope",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="cutter_weight",
 *          description="cutter_weight",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cutter_height",
 *          description="cutter_height",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="roll_dir",
 *          description="roll_dir",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="format_in_content",
 *          description="format_in_content",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="format_in_addcontent",
 *          description="format_in_addcontent",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="format_in_addcontent2",
 *          description="format_in_addcontent2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="format_in_addcontent3",
 *          description="format_in_addcontent3",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="format_in_envelope",
 *          description="format_in_envelope",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="pricesub",
 *          description="pricesub",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="pricetotal",
 *          description="pricetotal",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class OrderCalculation extends Model
{

    public $table = 'orders_calculations';
    
    public $timestamps = false;



    public $fillable = [
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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'order_id' => 'integer',
        'product_format' => 'integer',
        'product_format_width' => 'integer',
        'product_format_height' => 'integer',
        'product_format_width_open' => 'integer',
        'product_format_height_open' => 'integer',
        'product_pages_content' => 'integer',
        'product_pages_addcontent' => 'integer',
        'product_pages_envelope' => 'integer',
        'product_amount' => 'integer',
        'product_sorts' => 'integer',
        'paper_content' => 'integer',
        'paper_content_width' => 'integer',
        'paper_content_height' => 'integer',
        'paper_content_weight' => 'integer',
        'paper_addcontent' => 'integer',
        'paper_addcontent_width' => 'integer',
        'paper_addcontent_height' => 'integer',
        'paper_addcontent_weight' => 'integer',
        'paper_envelope' => 'integer',
        'paper_envelope_width' => 'integer',
        'paper_envelope_height' => 'integer',
        'paper_envelope_weight' => 'integer',
        'product_folding' => 'integer',
        'add_charge' => 'float',
        'margin' => 'float',
        'discount' => 'float',
        'chromaticities_content' => 'integer',
        'chromaticities_addcontent' => 'integer',
        'chromaticities_envelope' => 'integer',
        'envelope_height_open' => 'integer',
        'envelope_width_open' => 'integer',
        'paper_content_grant' => 'integer',
        'paper_addcontent_grant' => 'integer',
        'paper_envelope_grant' => 'integer',
        'text_processing' => 'string',
        'foldscheme_content' => 'string',
        'foldscheme_addcontent' => 'string',
        'foldscheme_envelope' => 'string',
        'crtdat' => 'integer',
        'crtusr' => 'integer',
        'upddat' => 'integer',
        'updusr' => 'integer',
        'product_pages_addcontent2' => 'integer',
        'paper_addcontent2' => 'integer',
        'paper_addcontent2_width' => 'integer',
        'paper_addcontent2_height' => 'integer',
        'paper_addcontent2_weight' => 'integer',
        'chromaticities_addcontent2' => 'integer',
        'paper_addcontent2_grant' => 'integer',
        'foldscheme_addcontent2' => 'string',
        'product_pages_addcontent3' => 'integer',
        'paper_addcontent3' => 'integer',
        'paper_addcontent3_width' => 'integer',
        'paper_addcontent3_height' => 'integer',
        'paper_addcontent3_weight' => 'integer',
        'chromaticities_addcontent3' => 'integer',
        'paper_addcontent3_grant' => 'integer',
        'foldscheme_addcontent3' => 'string',
        'cut_content' => 'float',
        'cut_addcontent' => 'float',
        'cut_addcontent2' => 'float',
        'cut_addcontent3' => 'float',
        'cut_envelope' => 'float',
        'cutter_weight' => 'integer',
        'cutter_height' => 'integer',
        'roll_dir' => 'integer',
        'title' => 'string',
        'format_in_content' => 'string',
        'format_in_addcontent' => 'string',
        'format_in_addcontent2' => 'string',
        'format_in_addcontent3' => 'string',
        'format_in_envelope' => 'string',
        'pricesub' => 'float',
        'pricetotal' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    protected $with = array(
        'ordermachines',
    );

    /**
     * @return mixed
     */
    public function ordermachines()
    {
        return $this->hasMany('App\Models\OrderMachine', 'calc_id', 'id');
    }

    
}
