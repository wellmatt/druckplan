<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Machine",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="lector_id",
 *          description="lector_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="document_text",
 *          description="document_text",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="price",
 *          description="price",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="border_left",
 *          description="border_left",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="border_right",
 *          description="border_right",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="border_top",
 *          description="border_top",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="border_bottom",
 *          description="border_bottom",
 *          type="integer",
 *          format="int32"
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
 *          property="time_platechange",
 *          description="time_platechange",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="time_colorchange",
 *          description="time_colorchange",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="time_base",
 *          description="time_base",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="units_per_hour",
 *          description="units_per_hour",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="finish_plate_cost",
 *          description="finish_plate_cost",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="finish_paper_cost",
 *          description="finish_paper_cost",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="maxhours",
 *          description="maxhours",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_size_height",
 *          description="paper_size_height",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_size_width",
 *          description="paper_size_width",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_size_min_height",
 *          description="paper_size_min_height",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paper_size_min_width",
 *          description="paper_size_min_width",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="difficulty",
 *          description="difficulty",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="time_setup_stations",
 *          description="time_setup_stations",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="time_signatures",
 *          description="time_signatures",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="time_envelope",
 *          description="time_envelope",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="time_trimmer",
 *          description="time_trimmer",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="time_stacker",
 *          description="time_stacker",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="crtdat",
 *          description="crtdat",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="crtusr",
 *          description="crtusr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="upddat",
 *          description="upddat",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updusr",
 *          description="updusr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cutprice",
 *          description="cutprice",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="internaltext",
 *          description="internaltext",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="hersteller",
 *          description="hersteller",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="baujahr",
 *          description="baujahr",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="DPHeight",
 *          description="DPHeight",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="DPWidth",
 *          description="DPWidth",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="breaks",
 *          description="breaks",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="breaks_time",
 *          description="breaks_time",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="machurl",
 *          description="machurl",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="color",
 *          description="color",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="maxstacksize",
 *          description="maxstacksize",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Machine extends Model
{

    public $table = 'machines';
    
    public $timestamps = false;



    public $fillable = [
        'lector_id',
        'state',
        'type',
        'group',
        'name',
        'document_text',
        'pricebase',
        'price',
        'border_left',
        'border_right',
        'border_top',
        'border_bottom',
        'colors_front',
        'colors_back',
        'time_platechange',
        'time_colorchange',
        'time_base',
        'units_per_hour',
        'unit',
        'finish',
        'finish_plate_cost',
        'finish_paper_cost',
        'maxhours',
        'paper_size_height',
        'paper_size_width',
        'paper_size_min_height',
        'paper_size_min_width',
        'difficulty',
        'time_setup_stations',
        'anz_stations',
        'pages_per_station',
        'anz_signatures',
        'time_signatures',
        'time_envelope',
        'time_trimmer',
        'time_stacker',
        'crtdat',
        'crtusr',
        'upddat',
        'updusr',
        'cutprice',
        'umschl_umst',
        'internaltext',
        'hersteller',
        'baujahr',
        'DPHeight',
        'DPWidth',
        'breaks',
        'breaks_time',
        'machurl',
        'color',
        'maxstacksize'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'lector_id' => 'integer',
        'name' => 'string',
        'document_text' => 'string',
        'price' => 'float',
        'border_left' => 'integer',
        'border_right' => 'integer',
        'border_top' => 'integer',
        'border_bottom' => 'integer',
        'units_per_hour' => 'integer',
        'finish_plate_cost' => 'float',
        'finish_paper_cost' => 'float',
        'paper_size_height' => 'integer',
        'paper_size_width' => 'integer',
        'paper_size_min_height' => 'integer',
        'paper_size_min_width' => 'integer',
        'time_setup_stations' => 'float',
        'time_signatures' => 'integer',
        'time_envelope' => 'integer',
        'time_trimmer' => 'integer',
        'time_stacker' => 'integer',
        'crtdat' => 'integer',
        'crtusr' => 'integer',
        'upddat' => 'integer',
        'updusr' => 'integer',
        'cutprice' => 'float',
        'internaltext' => 'string',
        'hersteller' => 'string',
        'baujahr' => 'string',
        'DPHeight' => 'float',
        'DPWidth' => 'float',
        'breaks' => 'integer',
        'breaks_time' => 'integer',
        'machurl' => 'string',
        'color' => 'string',
        'maxstacksize' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    protected $with = array(
        'machinechromaticities',
        'machinedifficulties',
        'machinelocks',
        'machinequalifiedusers',
        'machineunitsperhour',
        'machineworktimes',
    );

    /**
     * @return mixed
     */
    public function machinechromaticities()
    {
        return $this->hasMany('App\Models\MachineChromaticity', 'machine_id', 'id');
    }

    /**
     * @return mixed
     */
    public function machinedifficulties()
    {
        return $this->hasMany('App\Models\MachineDifficulty', 'machine_id', 'id');
    }

    /**
     * @return mixed
     */
    public function machinelocks()
    {
        return $this->hasMany('App\Models\MachineLock', 'machineid', 'id');
    }

    /**
     * @return mixed
     */
    public function machinequalifiedusers()
    {
        return $this->hasMany('App\Models\MachineQualifiedUser', 'machine', 'id');
    }

    /**
     * @return mixed
     */
    public function machineunitsperhour()
    {
        return $this->hasMany('App\Models\MachineUnitPerHour', 'machine_id', 'id');
    }

    /**
     * @return mixed
     */
    public function machineworktimes()
    {
        return $this->hasMany('App\Models\MachineWorkTime', 'machine', 'id');
    }

    
}
