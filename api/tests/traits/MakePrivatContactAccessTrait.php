<?php

use Faker\Factory as Faker;
use App\Models\PrivatContactAccess;
use App\Repositories\PrivatContactAccessRepository;

trait MakePrivatContactAccessTrait
{
    /**
     * Create fake instance of PrivatContactAccess and save it in database
     *
     * @param array $privatContactAccessFields
     * @return PrivatContactAccess
     */
    public function makePrivatContactAccess($privatContactAccessFields = [])
    {
        /** @var PrivatContactAccessRepository $privatContactAccessRepo */
        $privatContactAccessRepo = App::make(PrivatContactAccessRepository::class);
        $theme = $this->fakePrivatContactAccessData($privatContactAccessFields);
        return $privatContactAccessRepo->create($theme);
    }

    /**
     * Get fake instance of PrivatContactAccess
     *
     * @param array $privatContactAccessFields
     * @return PrivatContactAccess
     */
    public function fakePrivatContactAccess($privatContactAccessFields = [])
    {
        return new PrivatContactAccess($this->fakePrivatContactAccessData($privatContactAccessFields));
    }

    /**
     * Get fake data of PrivatContactAccess
     *
     * @param array $postFields
     * @return array
     */
    public function fakePrivatContactAccessData($privatContactAccessFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'prvtc_id' => $fake->randomDigitNotNull,
            'userid' => $fake->randomDigitNotNull
        ], $privatContactAccessFields);
    }
}
