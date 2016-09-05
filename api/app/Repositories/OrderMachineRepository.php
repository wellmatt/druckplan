<?php

namespace App\Repositories;

use App\Models\OrderMachine;
use InfyOm\Generator\Common\BaseRepository;

class OrderMachineRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
     * Configure the Model
     **/
    public function model()
    {
        return OrderMachine::class;
    }
}
