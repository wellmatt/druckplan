<?php

use Faker\Factory as Faker;
use App\Models\TicketLog;
use App\Repositories\TicketLogRepository;

trait MakeTicketLogTrait
{
    /**
     * Create fake instance of TicketLog and save it in database
     *
     * @param array $ticketLogFields
     * @return TicketLog
     */
    public function makeTicketLog($ticketLogFields = [])
    {
        /** @var TicketLogRepository $ticketLogRepo */
        $ticketLogRepo = App::make(TicketLogRepository::class);
        $theme = $this->fakeTicketLogData($ticketLogFields);
        return $ticketLogRepo->create($theme);
    }

    /**
     * Get fake instance of TicketLog
     *
     * @param array $ticketLogFields
     * @return TicketLog
     */
    public function fakeTicketLog($ticketLogFields = [])
    {
        return new TicketLog($this->fakeTicketLogData($ticketLogFields));
    }

    /**
     * Get fake data of TicketLog
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTicketLogData($ticketLogFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'ticket' => $fake->randomDigitNotNull,
            'crtusr' => $fake->randomDigitNotNull,
            'date' => $fake->randomDigitNotNull,
            'entry' => $fake->text
        ], $ticketLogFields);
    }
}
