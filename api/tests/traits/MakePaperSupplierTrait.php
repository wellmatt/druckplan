<?php

use Faker\Factory as Faker;
use App\Models\PaperSupplier;
use App\Repositories\PaperSupplierRepository;

trait MakePaperSupplierTrait
{
    /**
     * Create fake instance of PaperSupplier and save it in database
     *
     * @param array $paperSupplierFields
     * @return PaperSupplier
     */
    public function makePaperSupplier($paperSupplierFields = [])
    {
        /** @var PaperSupplierRepository $paperSupplierRepo */
        $paperSupplierRepo = App::make(PaperSupplierRepository::class);
        $theme = $this->fakePaperSupplierData($paperSupplierFields);
        return $paperSupplierRepo->create($theme);
    }

    /**
     * Get fake instance of PaperSupplier
     *
     * @param array $paperSupplierFields
     * @return PaperSupplier
     */
    public function fakePaperSupplier($paperSupplierFields = [])
    {
        return new PaperSupplier($this->fakePaperSupplierData($paperSupplierFields));
    }

    /**
     * Get fake data of PaperSupplier
     *
     * @param array $postFields
     * @return array
     */
    public function fakePaperSupplierData($paperSupplierFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'paper_id' => $fake->randomDigitNotNull,
            'supplier_id' => $fake->randomDigitNotNull,
            'description' => $fake->word
        ], $paperSupplierFields);
    }
}
