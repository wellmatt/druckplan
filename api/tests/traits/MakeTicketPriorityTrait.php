<?php

use Faker\Factory as Faker;
use App\Models\TicketPriority;
use App\Repositories\TicketPriorityRepository;

trait MakeTicketPriorityTrait
{
    /**
     * Create fake instance of TicketPriority and save it in database
     *
     * @param array $ticketPriorityFields
     * @return TicketPriority
     */
    public function makeTicketPriority($ticketPriorityFields = [])
    {
        /** @var TicketPriorityRepository $ticketPriorityRepo */
        $ticketPriorityRepo = App::make(TicketPriorityRepository::class);
        $theme = $this->fakeTicketPriorityData($ticketPriorityFields);
        return $ticketPriorityRepo->create($theme);
    }

    /**
     * Get fake instance of TicketPriority
     *
     * @param array $ticketPriorityFields
     * @return TicketPriority
     */
    public function fakeTicketPriority($ticketPriorityFields = [])
    {
        return new TicketPriority($this->fakeTicketPriorityData($ticketPriorityFields));
    }

    /**
     * Get fake data of TicketPriority
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTicketPriorityData($ticketPriorityFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'title' => $fake->word,
            'value' => $fake->randomDigitNotNull,
            'protected' => $fake->word
        ], $ticketPriorityFields);
    }
}
