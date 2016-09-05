<?php

use Faker\Factory as Faker;
use App\Models\PersonalizationOrder;
use App\Repositories\PersonalizationOrderRepository;

trait MakePersonalizationOrderTrait
{
    /**
     * Create fake instance of PersonalizationOrder and save it in database
     *
     * @param array $personalizationOrderFields
     * @return PersonalizationOrder
     */
    public function makePersonalizationOrder($personalizationOrderFields = [])
    {
        /** @var PersonalizationOrderRepository $personalizationOrderRepo */
        $personalizationOrderRepo = App::make(PersonalizationOrderRepository::class);
        $theme = $this->fakePersonalizationOrderData($personalizationOrderFields);
        return $personalizationOrderRepo->create($theme);
    }

    /**
     * Get fake instance of PersonalizationOrder
     *
     * @param array $personalizationOrderFields
     * @return PersonalizationOrder
     */
    public function fakePersonalizationOrder($personalizationOrderFields = [])
    {
        return new PersonalizationOrder($this->fakePersonalizationOrderData($personalizationOrderFields));
    }

    /**
     * Get fake data of PersonalizationOrder
     *
     * @param array $postFields
     * @return array
     */
    public function fakePersonalizationOrderData($personalizationOrderFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'status' => $fake->word,
            'title' => $fake->word,
            'persoid' => $fake->randomDigitNotNull,
            'documentid' => $fake->randomDigitNotNull,
            'customerid' => $fake->randomDigitNotNull,
            'crtuser' => $fake->randomDigitNotNull,
            'crtdate' => $fake->randomDigitNotNull,
            'orderdate' => $fake->randomDigitNotNull,
            'comment' => $fake->text,
            'amount' => $fake->randomDigitNotNull,
            'contact_person_id' => $fake->randomDigitNotNull,
            'deliveryaddress_id' => $fake->randomDigitNotNull
        ], $personalizationOrderFields);
    }
}
