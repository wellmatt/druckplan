<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Foldtype",
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
 *          property="beschreibung",
 *          description="beschreibung",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="vertical",
 *          description="vertical",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="horizontal",
 *          description="horizontal",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="picture",
 *          description="picture",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="breaks",
 *          description="breaks",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Foldtype extends Model
{

    public $table = 'foldtypes';
    
    public $timestamps = false;



    public $fillable = [
        'status',
        'name',
        'beschreibung',
        'vertical',
        'horizontal',
        'picture',
        'breaks'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'beschreibung' => 'string',
        'picture' => 'string',
        'breaks' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
