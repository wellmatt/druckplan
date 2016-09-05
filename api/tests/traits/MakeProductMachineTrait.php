<?php

use Faker\Factory as Faker;
use App\Models\ProductMachine;
use App\Repositories\ProductMachineRepository;

trait MakeProductMachineTrait
{
    /**
     * Create fake instance of ProductMachine and save it in database
     *
     * @param array $productMachineFields
     * @return ProductMachine
     */
    public function makeProductMachine($productMachineFields = [])
    {
        /** @var ProductMachineRepository $productMachineRepo */
        $productMachineRepo = App::make(ProductMachineRepository::class);
        $theme = $this->fakeProductMachineData($productMachineFields);
        return $productMachineRepo->create($theme);
    }

    /**
     * Get fake instance of ProductMachine
     *
     * @param array $productMachineFields
     * @return ProductMachine
     */
    public function fakeProductMachine($productMachineFields = [])
    {
        return new ProductMachine($this->fakeProductMachineData($productMachineFields));
    }

    /**
     * Get fake data of ProductMachine
     *
     * @param array $postFields
     * @return array
     */
    public function fakeProductMachineData($productMachineFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'product_id' => $fake->randomDigitNotNull,
            'machine_id' => $fake->randomDigitNotNull,
            'default' => $fake->word,
            'minimum' => $fake->randomDigitNotNull,
            'maximum' => $fake->randomDigitNotNull
        ], $productMachineFields);
    }
}
