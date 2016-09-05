<?php

use Faker\Factory as Faker;
use App\Models\Ticket;
use App\Repositories\TicketRepository;

trait MakeTicketTrait
{
    /**
     * Create fake instance of Ticket and save it in database
     *
     * @param array $ticketFields
     * @return Ticket
     */
    public function makeTicket($ticketFields = [])
    {
        /** @var TicketRepository $ticketRepo */
        $ticketRepo = App::make(TicketRepository::class);
        $theme = $this->fakeTicketData($ticketFields);
        return $ticketRepo->create($theme);
    }

    /**
     * Get fake instance of Ticket
     *
     * @param array $ticketFields
     * @return Ticket
     */
    public function fakeTicket($ticketFields = [])
    {
        return new Ticket($this->fakeTicketData($ticketFields));
    }

    /**
     * Get fake data of Ticket
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTicketData($ticketFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'title' => $fake->word,
            'crtdate' => $fake->randomDigitNotNull,
            'crtuser' => $fake->randomDigitNotNull,
            'duedate' => $fake->randomDigitNotNull,
            'closedate' => $fake->randomDigitNotNull,
            'closeuser' => $fake->randomDigitNotNull,
            'editdate' => $fake->randomDigitNotNull,
            'number' => $fake->word,
            'customer' => $fake->randomDigitNotNull,
            'customer_cp' => $fake->randomDigitNotNull,
            'assigned_user' => $fake->randomDigitNotNull,
            'assigned_group' => $fake->randomDigitNotNull,
            'state' => $fake->word,
            'category' => $fake->randomDigitNotNull,
            'priority' => $fake->randomDigitNotNull,
            'source' => $fake->word,
            'tourmarker' => $fake->word,
            'planned_time' => $fake->randomDigitNotNull
        ], $ticketFields);
    }
}
