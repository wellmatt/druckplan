<?php

namespace App\Repositories;

use App\Models\Machine;
use InfyOm\Generator\Common\BaseRepository;

class MachineRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
     * Configure the Model
     **/
    public function model()
    {
        return Machine::class;
    }
}
