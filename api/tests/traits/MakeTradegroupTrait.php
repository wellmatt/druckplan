<?php

use Faker\Factory as Faker;
use App\Models\Tradegroup;
use App\Repositories\TradegroupRepository;

trait MakeTradegroupTrait
{
    /**
     * Create fake instance of Tradegroup and save it in database
     *
     * @param array $tradegroupFields
     * @return Tradegroup
     */
    public function makeTradegroup($tradegroupFields = [])
    {
        /** @var TradegroupRepository $tradegroupRepo */
        $tradegroupRepo = App::make(TradegroupRepository::class);
        $theme = $this->fakeTradegroupData($tradegroupFields);
        return $tradegroupRepo->create($theme);
    }

    /**
     * Get fake instance of Tradegroup
     *
     * @param array $tradegroupFields
     * @return Tradegroup
     */
    public function fakeTradegroup($tradegroupFields = [])
    {
        return new Tradegroup($this->fakeTradegroupData($tradegroupFields));
    }

    /**
     * Get fake data of Tradegroup
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTradegroupData($tradegroupFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'tradegroup_state' => $fake->randomDigitNotNull,
            'tradegroup_title' => $fake->word,
            'tradegroup_desc' => $fake->text,
            'tradegroup_shoprel' => $fake->randomDigitNotNull,
            'tradegroup_parentid' => $fake->randomDigitNotNull
        ], $tradegroupFields);
    }
}
