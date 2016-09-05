<?php

use Faker\Factory as Faker;
use App\Models\SupOrder;
use App\Repositories\SupOrderRepository;

trait MakeSupOrderTrait
{
    /**
     * Create fake instance of SupOrder and save it in database
     *
     * @param array $supOrderFields
     * @return SupOrder
     */
    public function makeSupOrder($supOrderFields = [])
    {
        /** @var SupOrderRepository $supOrderRepo */
        $supOrderRepo = App::make(SupOrderRepository::class);
        $theme = $this->fakeSupOrderData($supOrderFields);
        return $supOrderRepo->create($theme);
    }

    /**
     * Get fake instance of SupOrder
     *
     * @param array $supOrderFields
     * @return SupOrder
     */
    public function fakeSupOrder($supOrderFields = [])
    {
        return new SupOrder($this->fakeSupOrderData($supOrderFields));
    }

    /**
     * Get fake data of SupOrder
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSupOrderData($supOrderFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'number' => $fake->word,
            'supplier' => $fake->randomDigitNotNull,
            'title' => $fake->word,
            'eta' => $fake->randomDigitNotNull,
            'paymentterm' => $fake->randomDigitNotNull,
            'status' => $fake->word,
            'invoiceaddress' => $fake->randomDigitNotNull,
            'deliveryaddress' => $fake->randomDigitNotNull,
            'crtdate' => $fake->randomDigitNotNull,
            'crtuser' => $fake->randomDigitNotNull,
            'cpinternal' => $fake->randomDigitNotNull,
            'cpexternal' => $fake->randomDigitNotNull
        ], $supOrderFields);
    }
}
