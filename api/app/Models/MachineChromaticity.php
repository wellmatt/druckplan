<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="MachineChromaticity",
 *      required={""},
 *      @SWG\Property(
 *          property="machine_id",
 *          description="machine_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chroma_id",
 *          description="chroma_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="clickprice",
 *          description="clickprice",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class MachineChromaticity extends Model
{

    public $table = 'machines_chromaticities';
    
    public $timestamps = false;



    public $fillable = [
        'machine_id',
        'chroma_id',
        'clickprice'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'machine_id' => 'integer',
        'chroma_id' => 'integer',
        'clickprice' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
