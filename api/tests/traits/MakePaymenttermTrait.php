<?php

use Faker\Factory as Faker;
use App\Models\Paymentterm;
use App\Repositories\PaymenttermRepository;

trait MakePaymenttermTrait
{
    /**
     * Create fake instance of Paymentterm and save it in database
     *
     * @param array $paymenttermFields
     * @return Paymentterm
     */
    public function makePaymentterm($paymenttermFields = [])
    {
        /** @var PaymenttermRepository $paymenttermRepo */
        $paymenttermRepo = App::make(PaymenttermRepository::class);
        $theme = $this->fakePaymenttermData($paymenttermFields);
        return $paymenttermRepo->create($theme);
    }

    /**
     * Get fake instance of Paymentterm
     *
     * @param array $paymenttermFields
     * @return Paymentterm
     */
    public function fakePaymentterm($paymenttermFields = [])
    {
        return new Paymentterm($this->fakePaymenttermData($paymenttermFields));
    }

    /**
     * Get fake data of Paymentterm
     *
     * @param array $postFields
     * @return array
     */
    public function fakePaymenttermData($paymenttermFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'active' => $fake->word,
            'client' => $fake->randomDigitNotNull,
            'name1' => $fake->word,
            'comment' => $fake->text,
            'skonto_days1' => $fake->word,
            'skonto1' => $fake->word,
            'skonto_days2' => $fake->word,
            'skonto2' => $fake->word,
            'netto_days' => $fake->word,
            'shop_rel' => $fake->randomDigitNotNull
        ], $paymenttermFields);
    }
}
