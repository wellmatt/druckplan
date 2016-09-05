<?php

use Faker\Factory as Faker;
use App\Models\Collectiveinvoice;
use App\Repositories\CollectiveinvoiceRepository;

trait MakeCollectiveinvoiceTrait
{
    /**
     * Create fake instance of Collectiveinvoice and save it in database
     *
     * @param array $collectiveinvoiceFields
     * @return Collectiveinvoice
     */
    public function makeCollectiveinvoice($collectiveinvoiceFields = [])
    {
        /** @var CollectiveinvoiceRepository $collectiveinvoiceRepo */
        $collectiveinvoiceRepo = App::make(CollectiveinvoiceRepository::class);
        $theme = $this->fakeCollectiveinvoiceData($collectiveinvoiceFields);
        return $collectiveinvoiceRepo->create($theme);
    }

    /**
     * Get fake instance of Collectiveinvoice
     *
     * @param array $collectiveinvoiceFields
     * @return Collectiveinvoice
     */
    public function fakeCollectiveinvoice($collectiveinvoiceFields = [])
    {
        return new Collectiveinvoice($this->fakeCollectiveinvoiceData($collectiveinvoiceFields));
    }

    /**
     * Get fake data of Collectiveinvoice
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCollectiveinvoiceData($collectiveinvoiceFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'status' => $fake->word,
            'title' => $fake->word,
            'number' => $fake->word,
            'deliverycosts' => $fake->randomDigitNotNull,
            'comment' => $fake->text,
            'client' => $fake->randomDigitNotNull,
            'businesscontact' => $fake->randomDigitNotNull,
            'deliveryterm' => $fake->randomDigitNotNull,
            'paymentterm' => $fake->randomDigitNotNull,
            'deliveryaddress' => $fake->randomDigitNotNull,
            'invoiceaddress' => $fake->randomDigitNotNull,
            'crtdate' => $fake->randomDigitNotNull,
            'crtuser' => $fake->randomDigitNotNull,
            'uptdate' => $fake->randomDigitNotNull,
            'uptuser' => $fake->randomDigitNotNull,
            'intent' => $fake->word,
            'intern_contactperson' => $fake->randomDigitNotNull,
            'cust_message' => $fake->word,
            'cust_sign' => $fake->word,
            'custContactperson' => $fake->randomDigitNotNull,
            'needs_planning' => $fake->word,
            'deliverydate' => $fake->randomDigitNotNull,
            'rdyfordispatch' => $fake->word,
            'ext_comment' => $fake->text,
            'thirdparty' => $fake->word,
            'thirdpartycomment' => $fake->text,
            'ticket' => $fake->randomDigitNotNull,
            'offer_header' => $fake->text,
            'offer_footer' => $fake->text,
            'offerconfirm_header' => $fake->text,
            'offerconfirm_footer' => $fake->text,
            'factory_header' => $fake->text,
            'factory_footer' => $fake->text,
            'delivery_header' => $fake->text,
            'delivery_footer' => $fake->text,
            'invoice_header' => $fake->text,
            'invoice_footer' => $fake->text,
            'revert_header' => $fake->text,
            'revert_footer' => $fake->text
        ], $collectiveinvoiceFields);
    }
}
