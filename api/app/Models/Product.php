<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Product",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="picture",
 *          description="picture",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="pages_from",
 *          description="pages_from",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="pages_to",
 *          description="pages_to",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="pages_step",
 *          description="pages_step",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="factor_width",
 *          description="factor_width",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="factor_height",
 *          description="factor_height",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="taxes",
 *          description="taxes",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="grant_paper",
 *          description="grant_paper",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="type",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="text_offer",
 *          description="text_offer",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="text_offerconfirm",
 *          description="text_offerconfirm",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="text_invoice",
 *          description="text_invoice",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="text_processing",
 *          description="text_processing",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="shop_rel",
 *          description="shop_rel",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tradegroup",
 *          description="tradegroup",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Product extends Model
{

    public $table = 'products';
    
    public $timestamps = false;



    public $fillable = [
        'state',
        'name',
        'description',
        'picture',
        'pages_from',
        'pages_to',
        'pages_step',
        'has_content',
        'has_addcontent',
        'has_envelope',
        'factor_width',
        'factor_height',
        'taxes',
        'grant_paper',
        'type',
        'text_offer',
        'text_offerconfirm',
        'text_invoice',
        'text_processing',
        'shop_rel',
        'tradegroup',
        'is_individual',
        'has_addcontent2',
        'has_addcontent3',
        'load_dummydata',
        'singleplateset',
        'blockplateset'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'picture' => 'string',
        'factor_width' => 'float',
        'factor_height' => 'float',
        'taxes' => 'float',
        'grant_paper' => 'integer',
        'text_offer' => 'string',
        'text_offerconfirm' => 'string',
        'text_invoice' => 'string',
        'text_processing' => 'string',
        'shop_rel' => 'integer',
        'tradegroup' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    protected $with = array(
        'productchromaticities',
        'productformats',
        'productmachines',
        'productpapers',
    );

    /**
     * @return mixed
     */
    public function productchromaticities()
    {
        return $this->hasMany('App\Models\ProductChromaticity', 'product_id', 'id');
    }

    /**
     * @return mixed
     */
    public function productformats()
    {
        return $this->hasMany('App\Models\ProductFormat', 'product_id', 'id');
    }

    /**
     * @return mixed
     */
    public function productmachines()
    {
        return $this->hasMany('App\Models\ProductMachine', 'product_id', 'id');
    }

    /**
     * @return mixed
     */
    public function productpapers()
    {
        return $this->hasMany('App\Models\ProductPaper', 'product_id', 'id');
    }

    
}
