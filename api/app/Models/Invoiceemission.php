<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Invoiceemission",
 *      required={""},
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
 *          property="invc_number",
 *          description="invc_number",
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
 *          property="invc_payed",
 *          description="invc_payed",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invc_payed_dat",
 *          description="invc_payed_dat",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invc_payable_dat",
 *          description="invc_payable_dat",
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
 *      ),
 *      @SWG\Property(
 *          property="invc_orders",
 *          description="invc_orders",
 *          type="string"
 *      )
 * )
 */
class Invoiceemission extends Model
{

    public $table = 'invoices_emissions';
    
    public $timestamps = false;



    public $fillable = [
        'invc_title',
        'invc_number',
        'invc_price_netto',
        'invc_taxes_active',
        'invc_payed',
        'invc_payed_dat',
        'invc_payable_dat',
        'invc_crtusr',
        'invc_crtdat',
        'invc_companyid',
        'invc_supplierid',
        'invc_orders'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'invc_title' => 'string',
        'invc_number' => 'string',
        'invc_payed_dat' => 'integer',
        'invc_payable_dat' => 'integer',
        'invc_crtusr' => 'integer',
        'invc_crtdat' => 'integer',
        'invc_companyid' => 'integer',
        'invc_supplierid' => 'integer',
        'invc_orders' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
