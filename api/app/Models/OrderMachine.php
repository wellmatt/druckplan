<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="OrderMachine",
 *      required={""},
 *      @SWG\Property(
 *          property="info",
 *          description="info",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="calc_id",
 *          description="calc_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="machine_id",
 *          description="machine_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="machine_group",
 *          description="machine_group",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chromaticity_id",
 *          description="chromaticity_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="time",
 *          description="time",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="price",
 *          description="price",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="part",
 *          description="part",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplier_send_date",
 *          description="supplier_send_date",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplier_receive_date",
 *          description="supplier_receive_date",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplier_id",
 *          description="supplier_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplier_info",
 *          description="supplier_info",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplier_price",
 *          description="supplier_price",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="cutter_cuts",
 *          description="cutter_cuts",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="roll_dir",
 *          description="roll_dir",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="format_in_width",
 *          description="format_in_width",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="format_in_height",
 *          description="format_in_height",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="format_out_width",
 *          description="format_out_width",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="format_out_height",
 *          description="format_out_height",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="color_detail",
 *          description="color_detail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="special_margin",
 *          description="special_margin",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="special_margin_text",
 *          description="special_margin_text",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="foldtype",
 *          description="foldtype",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="labelcount",
 *          description="labelcount",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rollcount",
 *          description="rollcount",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class OrderMachine extends Model
{

    public $table = 'orders_machines';
    
    public $timestamps = false;



    public $fillable = [
        'info',
        'calc_id',
        'machine_id',
        'machine_group',
        'chromaticity_id',
        'time',
        'price',
        'part',
        'finishing',
        'supplier_send_date',
        'supplier_receive_date',
        'supplier_id',
        'supplier_info',
        'supplier_price',
        'supplier_status',
        'umschl_umst',
        'umschl',
        'umst',
        'cutter_cuts',
        'roll_dir',
        'format_in_width',
        'format_in_height',
        'format_out_width',
        'format_out_height',
        'color_detail',
        'special_margin',
        'special_margin_text',
        'foldtype',
        'labelcount',
        'rollcount',
        'doubleutilization'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'info' => 'string',
        'calc_id' => 'integer',
        'machine_id' => 'integer',
        'chromaticity_id' => 'integer',
        'price' => 'float',
        'supplier_send_date' => 'integer',
        'supplier_receive_date' => 'integer',
        'supplier_id' => 'integer',
        'supplier_info' => 'string',
        'supplier_price' => 'float',
        'cutter_cuts' => 'integer',
        'roll_dir' => 'integer',
        'format_in_width' => 'integer',
        'format_in_height' => 'integer',
        'format_out_width' => 'integer',
        'format_out_height' => 'integer',
        'color_detail' => 'string',
        'special_margin' => 'float',
        'special_margin_text' => 'string',
        'foldtype' => 'integer',
        'labelcount' => 'integer',
        'rollcount' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
