<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Language",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="language",
 *          description="language",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="language_int",
 *          description="language_int",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="language_code",
 *          description="language_code",
 *          type="string"
 *      )
 * )
 */
class Language extends Model
{

    public $table = 'language';
    
    public $timestamps = false;



    public $fillable = [
        'id',
        'language',
        'language_int',
        'language_code',
        'language_active'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'language' => 'string',
        'language_int' => 'string',
        'language_code' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
