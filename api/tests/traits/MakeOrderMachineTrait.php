<?php

use Faker\Factory as Faker;
use App\Models\OrderMachine;
use App\Repositories\OrderMachineRepository;

trait MakeOrderMachineTrait
{
    /**
     * Create fake instance of OrderMachine and save it in database
     *
     * @param array $orderMachineFields
     * @return OrderMachine
     */
    public function makeOrderMachine($orderMachineFields = [])
    {
        /** @var OrderMachineRepository $orderMachineRepo */
        $orderMachineRepo = App::make(OrderMachineRepository::class);
        $theme = $this->fakeOrderMachineData($orderMachineFields);
        return $orderMachineRepo->create($theme);
    }

    /**
     * Get fake instance of OrderMachine
     *
     * @param array $orderMachineFields
     * @return OrderMachine
     */
    public function fakeOrderMachine($orderMachineFields = [])
    {
        return new OrderMachine($this->fakeOrderMachineData($orderMachineFields));
    }

    /**
     * Get fake data of OrderMachine
     *
     * @param array $postFields
     * @return array
     */
    public function fakeOrderMachineData($orderMachineFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'info' => $fake->text,
            'calc_id' => $fake->randomDigitNotNull,
            'machine_id' => $fake->randomDigitNotNull,
            'machine_group' => $fake->word,
            'chromaticity_id' => $fake->randomDigitNotNull,
            'time' => $fake->word,
            'price' => $fake->randomDigitNotNull,
            'part' => $fake->word,
            'finishing' => $fake->word,
            'supplier_send_date' => $fake->randomDigitNotNull,
            'supplier_receive_date' => $fake->randomDigitNotNull,
            'supplier_id' => $fake->randomDigitNotNull,
            'supplier_info' => $fake->word,
            'supplier_price' => $fake->randomDigitNotNull,
            'supplier_status' => $fake->word,
            'umschl_umst' => $fake->word,
            'umschl' => $fake->word,
            'umst' => $fake->word,
            'cutter_cuts' => $fake->randomDigitNotNull,
            'roll_dir' => $fake->randomDigitNotNull,
            'format_in_width' => $fake->randomDigitNotNull,
            'format_in_height' => $fake->randomDigitNotNull,
            'format_out_width' => $fake->randomDigitNotNull,
            'format_out_height' => $fake->randomDigitNotNull,
            'color_detail' => $fake->text,
            'special_margin' => $fake->randomDigitNotNull,
            'special_margin_text' => $fake->word,
            'foldtype' => $fake->randomDigitNotNull,
            'labelcount' => $fake->randomDigitNotNull,
            'rollcount' => $fake->randomDigitNotNull,
            'doubleutilization' => $fake->word
        ], $orderMachineFields);
    }
}
