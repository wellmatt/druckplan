<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PaperWeight",
 *      required={""},
 *      @SWG\Property(
 *          property="paper_id",
 *          description="paper_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="weight",
 *          description="weight",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class PaperWeight extends Model
{

    public $table = 'papers_weights';
    
    public $timestamps = false;



    public $fillable = [
        'paper_id',
        'weight'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'paper_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
