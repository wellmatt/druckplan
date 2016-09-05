<?php

use Faker\Factory as Faker;
use App\Models\TicketState;
use App\Repositories\TicketStateRepository;

trait MakeTicketStateTrait
{
    /**
     * Create fake instance of TicketState and save it in database
     *
     * @param array $ticketStateFields
     * @return TicketState
     */
    public function makeTicketState($ticketStateFields = [])
    {
        /** @var TicketStateRepository $ticketStateRepo */
        $ticketStateRepo = App::make(TicketStateRepository::class);
        $theme = $this->fakeTicketStateData($ticketStateFields);
        return $ticketStateRepo->create($theme);
    }

    /**
     * Get fake instance of TicketState
     *
     * @param array $ticketStateFields
     * @return TicketState
     */
    public function fakeTicketState($ticketStateFields = [])
    {
        return new TicketState($this->fakeTicketStateData($ticketStateFields));
    }

    /**
     * Get fake data of TicketState
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTicketStateData($ticketStateFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'title' => $fake->word,
            'protected' => $fake->word,
            'colorcode' => $fake->word
        ], $ticketStateFields);
    }
}
