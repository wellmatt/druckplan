<?php

use Faker\Factory as Faker;
use App\Models\UserEmail;
use App\Repositories\UserEmailRepository;

trait MakeUserEmailTrait
{
    /**
     * Create fake instance of UserEmail and save it in database
     *
     * @param array $userEmailFields
     * @return UserEmail
     */
    public function makeUserEmail($userEmailFields = [])
    {
        /** @var UserEmailRepository $userEmailRepo */
        $userEmailRepo = App::make(UserEmailRepository::class);
        $theme = $this->fakeUserEmailData($userEmailFields);
        return $userEmailRepo->create($theme);
    }

    /**
     * Get fake instance of UserEmail
     *
     * @param array $userEmailFields
     * @return UserEmail
     */
    public function fakeUserEmail($userEmailFields = [])
    {
        return new UserEmail($this->fakeUserEmailData($userEmailFields));
    }

    /**
     * Get fake data of UserEmail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeUserEmailData($userEmailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'status' => $fake->word,
            'user_id' => $fake->word,
            'login' => $fake->word,
            'address' => $fake->word,
            'password' => $fake->word,
            'type' => $fake->word,
            'host' => $fake->word,
            'port' => $fake->word,
            'signature' => $fake->text,
            'use_imap' => $fake->word,
            'use_ssl' => $fake->word
        ], $userEmailFields);
    }
}
