<?php

use Faker\Factory as Faker;
use App\Models\TicketSource;
use App\Repositories\TicketSourceRepository;

trait MakeTicketSourceTrait
{
    /**
     * Create fake instance of TicketSource and save it in database
     *
     * @param array $ticketSourceFields
     * @return TicketSource
     */
    public function makeTicketSource($ticketSourceFields = [])
    {
        /** @var TicketSourceRepository $ticketSourceRepo */
        $ticketSourceRepo = App::make(TicketSourceRepository::class);
        $theme = $this->fakeTicketSourceData($ticketSourceFields);
        return $ticketSourceRepo->create($theme);
    }

    /**
     * Get fake instance of TicketSource
     *
     * @param array $ticketSourceFields
     * @return TicketSource
     */
    public function fakeTicketSource($ticketSourceFields = [])
    {
        return new TicketSource($this->fakeTicketSourceData($ticketSourceFields));
    }

    /**
     * Get fake data of TicketSource
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTicketSourceData($ticketSourceFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'title' => $fake->word,
            'default' => $fake->word
        ], $ticketSourceFields);
    }
}
