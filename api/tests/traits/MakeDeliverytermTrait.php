<?php

use Faker\Factory as Faker;
use App\Models\Deliveryterm;
use App\Repositories\DeliverytermRepository;

trait MakeDeliverytermTrait
{
    /**
     * Create fake instance of Deliveryterm and save it in database
     *
     * @param array $deliverytermFields
     * @return Deliveryterm
     */
    public function makeDeliveryterm($deliverytermFields = [])
    {
        /** @var DeliverytermRepository $deliverytermRepo */
        $deliverytermRepo = App::make(DeliverytermRepository::class);
        $theme = $this->fakeDeliverytermData($deliverytermFields);
        return $deliverytermRepo->create($theme);
    }

    /**
     * Get fake instance of Deliveryterm
     *
     * @param array $deliverytermFields
     * @return Deliveryterm
     */
    public function fakeDeliveryterm($deliverytermFields = [])
    {
        return new Deliveryterm($this->fakeDeliverytermData($deliverytermFields));
    }

    /**
     * Get fake data of Deliveryterm
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDeliverytermData($deliverytermFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'active' => $fake->word,
            'client' => $fake->randomDigitNotNull,
            'name1' => $fake->word,
            'comment' => $fake->text,
            'charges' => $fake->randomDigitNotNull,
            'shoprel' => $fake->randomDigitNotNull,
            'tax' => $fake->randomDigitNotNull
        ], $deliverytermFields);
    }
}
