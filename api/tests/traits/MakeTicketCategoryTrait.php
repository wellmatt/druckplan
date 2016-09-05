<?php

use Faker\Factory as Faker;
use App\Models\TicketCategory;
use App\Repositories\TicketCategoryRepository;

trait MakeTicketCategoryTrait
{
    /**
     * Create fake instance of TicketCategory and save it in database
     *
     * @param array $ticketCategoryFields
     * @return TicketCategory
     */
    public function makeTicketCategory($ticketCategoryFields = [])
    {
        /** @var TicketCategoryRepository $ticketCategoryRepo */
        $ticketCategoryRepo = App::make(TicketCategoryRepository::class);
        $theme = $this->fakeTicketCategoryData($ticketCategoryFields);
        return $ticketCategoryRepo->create($theme);
    }

    /**
     * Get fake instance of TicketCategory
     *
     * @param array $ticketCategoryFields
     * @return TicketCategory
     */
    public function fakeTicketCategory($ticketCategoryFields = [])
    {
        return new TicketCategory($this->fakeTicketCategoryData($ticketCategoryFields));
    }

    /**
     * Get fake data of TicketCategory
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTicketCategoryData($ticketCategoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'title' => $fake->word,
            'protected' => $fake->word,
            'sort' => $fake->randomDigitNotNull
        ], $ticketCategoryFields);
    }
}
