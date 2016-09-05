<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PersonalizationItem",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="xpos",
 *          description="xpos",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="ypos",
 *          description="ypos",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="height",
 *          description="height",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="width",
 *          description="width",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="boxtype",
 *          description="boxtype",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="personalization_id",
 *          description="personalization_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="text_size",
 *          description="text_size",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="font",
 *          description="font",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="color_c",
 *          description="color_c",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="color_m",
 *          description="color_m",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="color_y",
 *          description="color_y",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="color_k",
 *          description="color_k",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="spacing",
 *          description="spacing",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="dependency_id",
 *          description="dependency_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tab",
 *          description="tab",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="sort",
 *          description="sort",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class PersonalizationItem extends Model
{

    public $table = 'personalization_items';
    
    public $timestamps = false;



    public $fillable = [
        'status',
        'title',
        'xpos',
        'ypos',
        'height',
        'width',
        'boxtype',
        'personalization_id',
        'text_size',
        'justification',
        'font',
        'color_c',
        'color_m',
        'color_y',
        'color_k',
        'spacing',
        'dependency_id',
        'reverse',
        'predefined',
        'position',
        'readonly',
        'tab',
        'zzgroup',
        'sort'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'xpos' => 'float',
        'ypos' => 'float',
        'height' => 'float',
        'width' => 'float',
        'boxtype' => 'integer',
        'personalization_id' => 'integer',
        'text_size' => 'float',
        'spacing' => 'float',
        'dependency_id' => 'integer',
        'tab' => 'float',
        'sort' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
