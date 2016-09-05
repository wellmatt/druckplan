<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Invoicerevert",
 *      required={"rev_title","rev_number","rev_price_netto","rev_taxes_active","rev_payed","rev_payed_dat","rev_payable_dat","rev_crtusr","rev_crtdat","rev_companyid","rev_supplierid"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rev_title",
 *          description="rev_title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="rev_number",
 *          description="rev_number",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="rev_price_netto",
 *          description="rev_price_netto",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="rev_taxes_active",
 *          description="rev_taxes_active",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rev_payed",
 *          description="rev_payed",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rev_payed_dat",
 *          description="rev_payed_dat",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rev_payable_dat",
 *          description="rev_payable_dat",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rev_crtusr",
 *          description="rev_crtusr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rev_crtdat",
 *          description="rev_crtdat",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rev_companyid",
 *          description="rev_companyid",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rev_supplierid",
 *          description="rev_supplierid",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Invoicerevert extends Model
{

    public $table = 'invoices_reverts';
    
    public $timestamps = false;



    public $fillable = [
        'rev_title',
        'rev_number',
        'rev_price_netto',
        'rev_taxes_active',
        'rev_payed',
        'rev_payed_dat',
        'rev_payable_dat',
        'rev_crtusr',
        'rev_crtdat',
        'rev_companyid',
        'rev_supplierid'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'rev_title' => 'string',
        'rev_number' => 'string',
        'rev_payed_dat' => 'integer',
        'rev_payable_dat' => 'integer',
        'rev_crtusr' => 'integer',
        'rev_crtdat' => 'integer',
        'rev_companyid' => 'integer',
        'rev_supplierid' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
