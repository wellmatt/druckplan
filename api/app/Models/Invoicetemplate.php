<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Invoicetemplate",
 *      required={"invc_title","invc_price_netto","invc_taxes_active","invc_crtusr","invc_crtdat","invc_companyid","invc_supplierid"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invc_title",
 *          description="invc_title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="invc_price_netto",
 *          description="invc_price_netto",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="invc_taxes_active",
 *          description="invc_taxes_active",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invc_crtusr",
 *          description="invc_crtusr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invc_crtdat",
 *          description="invc_crtdat",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invc_companyid",
 *          description="invc_companyid",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invc_supplierid",
 *          description="invc_supplierid",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Invoicetemplate extends Model
{

    public $table = 'invoices_templates';
    
    public $timestamps = false;



    public $fillable = [
        'invc_title',
        'invc_price_netto',
        'invc_taxes_active',
        'invc_crtusr',
        'invc_crtdat',
        'invc_companyid',
        'invc_supplierid'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'invc_title' => 'string',
        'invc_crtusr' => 'integer',
        'invc_crtdat' => 'integer',
        'invc_companyid' => 'integer',
        'invc_supplierid' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
