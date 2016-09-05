<?php

use Faker\Factory as Faker;
use App\Models\User;
use App\Repositories\UserRepository;

trait MakeUserTrait
{
    /**
     * Create fake instance of User and save it in database
     *
     * @param array $userFields
     * @return User
     */
    public function makeUser($userFields = [])
    {
        /** @var UserRepository $userRepo */
        $userRepo = App::make(UserRepository::class);
        $theme = $this->fakeUserData($userFields);
        return $userRepo->create($theme);
    }

    /**
     * Get fake instance of User
     *
     * @param array $userFields
     * @return User
     */
    public function fakeUser($userFields = [])
    {
        return new User($this->fakeUserData($userFields));
    }

    /**
     * Get fake data of User
     *
     * @param array $postFields
     * @return array
     */
    public function fakeUserData($userFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'client' => $fake->randomDigitNotNull,
            'login' => $fake->word,
            'password' => $fake->word,
            'user_firstname' => $fake->word,
            'user_lastname' => $fake->word,
            'user_level' => $fake->randomDigitNotNull,
            'user_email' => $fake->word,
            'user_phone' => $fake->word,
            'user_signature' => $fake->text,
            'user_lang' => $fake->randomDigitNotNull,
            'user_active' => $fake->word,
            'user_forwardmail' => $fake->word,
            'telefon_ip' => $fake->word,
            'cal_birthdays' => $fake->word,
            'cal_tickets' => $fake->word,
            'cal_orders' => $fake->word,
            'w_mo' => $fake->randomDigitNotNull,
            'w_tu' => $fake->randomDigitNotNull,
            'w_we' => $fake->randomDigitNotNull,
            'w_th' => $fake->randomDigitNotNull,
            'w_fr' => $fake->randomDigitNotNull,
            'w_sa' => $fake->randomDigitNotNull,
            'w_su' => $fake->randomDigitNotNull,
            'w_month' => $fake->randomDigitNotNull,
            'avatar' => $fake->word,
            'homepage' => $fake->word,
            'BCshowOnlyOverview' => $fake->word
        ], $userFields);
    }
}
