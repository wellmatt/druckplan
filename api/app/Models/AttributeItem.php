<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AttributeItem",
 *      required={"status","attribute_id","title","input"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="attribute_id",
 *          description="attribute_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      )
 * )
 */
class AttributeItem extends Model
{

    public $table = 'attributes_items';
    
    public $timestamps = false;



    public $fillable = [
        'status',
        'attribute_id',
        'title',
        'input'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function article()
    {
        return $this->belongsTo('App\Models\Attribute', 'id', 'attribute_id');
    }

    
}
