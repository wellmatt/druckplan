<?php

use Faker\Factory as Faker;
use App\Models\Machine;
use App\Repositories\MachineRepository;

trait MakeMachineTrait
{
    /**
     * Create fake instance of Machine and save it in database
     *
     * @param array $machineFields
     * @return Machine
     */
    public function makeMachine($machineFields = [])
    {
        /** @var MachineRepository $machineRepo */
        $machineRepo = App::make(MachineRepository::class);
        $theme = $this->fakeMachineData($machineFields);
        return $machineRepo->create($theme);
    }

    /**
     * Get fake instance of Machine
     *
     * @param array $machineFields
     * @return Machine
     */
    public function fakeMachine($machineFields = [])
    {
        return new Machine($this->fakeMachineData($machineFields));
    }

    /**
     * Get fake data of Machine
     *
     * @param array $postFields
     * @return array
     */
    public function fakeMachineData($machineFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'lector_id' => $fake->randomDigitNotNull,
            'state' => $fake->word,
            'type' => $fake->word,
            'group' => $fake->word,
            'name' => $fake->word,
            'document_text' => $fake->text,
            'pricebase' => $fake->word,
            'price' => $fake->randomDigitNotNull,
            'border_left' => $fake->randomDigitNotNull,
            'border_right' => $fake->randomDigitNotNull,
            'border_top' => $fake->randomDigitNotNull,
            'border_bottom' => $fake->randomDigitNotNull,
            'colors_front' => $fake->word,
            'colors_back' => $fake->word,
            'time_platechange' => $fake->word,
            'time_colorchange' => $fake->word,
            'time_base' => $fake->word,
            'units_per_hour' => $fake->randomDigitNotNull,
            'unit' => $fake->word,
            'finish' => $fake->word,
            'finish_plate_cost' => $fake->randomDigitNotNull,
            'finish_paper_cost' => $fake->randomDigitNotNull,
            'maxhours' => $fake->word,
            'paper_size_height' => $fake->randomDigitNotNull,
            'paper_size_width' => $fake->randomDigitNotNull,
            'paper_size_min_height' => $fake->randomDigitNotNull,
            'paper_size_min_width' => $fake->randomDigitNotNull,
            'difficulty' => $fake->word,
            'time_setup_stations' => $fake->randomDigitNotNull,
            'anz_stations' => $fake->word,
            'pages_per_station' => $fake->word,
            'anz_signatures' => $fake->word,
            'time_signatures' => $fake->randomDigitNotNull,
            'time_envelope' => $fake->randomDigitNotNull,
            'time_trimmer' => $fake->randomDigitNotNull,
            'time_stacker' => $fake->randomDigitNotNull,
            'crtdat' => $fake->randomDigitNotNull,
            'crtusr' => $fake->randomDigitNotNull,
            'upddat' => $fake->randomDigitNotNull,
            'updusr' => $fake->randomDigitNotNull,
            'cutprice' => $fake->randomDigitNotNull,
            'umschl_umst' => $fake->word,
            'internaltext' => $fake->text,
            'hersteller' => $fake->word,
            'baujahr' => $fake->word,
            'DPHeight' => $fake->randomDigitNotNull,
            'DPWidth' => $fake->randomDigitNotNull,
            'breaks' => $fake->randomDigitNotNull,
            'breaks_time' => $fake->randomDigitNotNull,
            'machurl' => $fake->word,
            'color' => $fake->word,
            'maxstacksize' => $fake->randomDigitNotNull
        ], $machineFields);
    }
}
