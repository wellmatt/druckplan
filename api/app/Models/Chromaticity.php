<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Chromaticity",
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
 *          property="colors_front",
 *          description="colors_front",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="colors_back",
 *          description="colors_back",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="markup",
 *          description="markup",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="pricekg",
 *          description="pricekg",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class Chromaticity extends Model
{

    public $table = 'chromaticities';
    
    public $timestamps = false;



    public $fillable = [
        'name',
        'colors_front',
        'colors_back',
        'reverse_printing',
        'markup',
        'pricekg'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'markup' => 'float',
        'pricekg' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
