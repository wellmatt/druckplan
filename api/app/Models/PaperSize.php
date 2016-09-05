<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PaperSize",
 *      required={""},
 *      @SWG\Property(
 *          property="paper_id",
 *          description="paper_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="width",
 *          description="width",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="height",
 *          description="height",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class PaperSize extends Model
{

    public $table = 'papers_sizes';
    
    public $timestamps = false;



    public $fillable = [
        'paper_id',
        'width',
        'height'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'paper_id' => 'integer',
        'width' => 'integer',
        'height' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
