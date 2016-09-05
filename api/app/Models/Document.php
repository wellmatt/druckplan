<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Document",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="doc_name",
 *          description="doc_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="doc_req_id",
 *          description="doc_req_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="doc_req_module",
 *          description="doc_req_module",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="doc_type",
 *          description="doc_type",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="doc_hash",
 *          description="doc_hash",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="doc_crtdat",
 *          description="doc_crtdat",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="doc_crtusr",
 *          description="doc_crtusr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="doc_price_netto",
 *          description="doc_price_netto",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="doc_price_brutto",
 *          description="doc_price_brutto",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="doc_payable",
 *          description="doc_payable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="doc_payed",
 *          description="doc_payed",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="doc_warning_id",
 *          description="doc_warning_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="doc_storno_date",
 *          description="doc_storno_date",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_order_pid",
 *          description="paper_order_pid",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Document extends Model
{

    public $table = 'documents';
    
    public $timestamps = false;



    public $fillable = [
        'doc_name',
        'doc_req_id',
        'doc_req_module',
        'doc_type',
        'doc_hash',
        'doc_sent',
        'doc_crtdat',
        'doc_crtusr',
        'doc_price_netto',
        'doc_price_brutto',
        'doc_payable',
        'doc_payed',
        'doc_warning_id',
        'doc_reverse',
        'doc_storno_date',
        'paper_order_pid'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'doc_name' => 'string',
        'doc_req_id' => 'integer',
        'doc_type' => 'integer',
        'doc_hash' => 'string',
        'doc_crtdat' => 'integer',
        'doc_crtusr' => 'integer',
        'doc_price_netto' => 'float',
        'doc_price_brutto' => 'float',
        'doc_payable' => 'integer',
        'doc_payed' => 'integer',
        'doc_warning_id' => 'integer',
        'doc_storno_date' => 'integer',
        'paper_order_pid' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
