<?php

use Faker\Factory as Faker;
use App\Models\Order;
use App\Repositories\OrderRepository;

trait MakeOrderTrait
{
    /**
     * Create fake instance of Order and save it in database
     *
     * @param array $orderFields
     * @return Order
     */
    public function makeOrder($orderFields = [])
    {
        /** @var OrderRepository $orderRepo */
        $orderRepo = App::make(OrderRepository::class);
        $theme = $this->fakeOrderData($orderFields);
        return $orderRepo->create($theme);
    }

    /**
     * Get fake instance of Order
     *
     * @param array $orderFields
     * @return Order
     */
    public function fakeOrder($orderFields = [])
    {
        return new Order($this->fakeOrderData($orderFields));
    }

    /**
     * Get fake data of Order
     *
     * @param array $postFields
     * @return array
     */
    public function fakeOrderData($orderFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'number' => $fake->word,
            'status' => $fake->word,
            'businesscontact_id' => $fake->randomDigitNotNull,
            'product_id' => $fake->randomDigitNotNull,
            'title' => $fake->word,
            'notes' => $fake->text,
            'delivery_address_id' => $fake->randomDigitNotNull,
            'invoice_address_id' => $fake->randomDigitNotNull,
            'delivery_terms_id' => $fake->randomDigitNotNull,
            'payment_terms_id' => $fake->randomDigitNotNull,
            'delivery_date' => $fake->randomDigitNotNull,
            'delivery_cost' => $fake->randomDigitNotNull,
            'text_offer' => $fake->text,
            'text_offerconfirm' => $fake->text,
            'text_invoice' => $fake->text,
            'cust_contactperson' => $fake->randomDigitNotNull,
            'crtdat' => $fake->randomDigitNotNull,
            'crtusr' => $fake->randomDigitNotNull,
            'upddat' => $fake->randomDigitNotNull,
            'updusr' => $fake->randomDigitNotNull,
            'collectiveinvoice_id' => $fake->randomDigitNotNull,
            'intern_contactperson' => $fake->randomDigitNotNull,
            'cust_message' => $fake->word,
            'cust_sign' => $fake->word,
            'inv_amount' => $fake->randomDigitNotNull,
            'inv_price_update' => $fake->word,
            'deliv_amount' => $fake->randomDigitNotNull,
            'label_logo_active' => $fake->word,
            'label_box_amount' => $fake->randomDigitNotNull,
            'label_title' => $fake->word,
            'show_product' => $fake->word,
            'productname' => $fake->word,
            'show_price_per_thousand' => $fake->word,
            'paper_order_boegen' => $fake->word,
            'paper_order_price' => $fake->word,
            'paper_order_supplier' => $fake->randomDigitNotNull,
            'paper_order_calc' => $fake->randomDigitNotNull,
            'beilagen' => $fake->text,
            'articleid' => $fake->randomDigitNotNull
        ], $orderFields);
    }
}
