<?php

use Faker\Factory as Faker;
use App\Models\EventParticipant;
use App\Repositories\EventParticipantRepository;

trait MakeEventParticipantTrait
{
    /**
     * Create fake instance of EventParticipant and save it in database
     *
     * @param array $eventParticipantFields
     * @return EventParticipant
     */
    public function makeEventParticipant($eventParticipantFields = [])
    {
        /** @var EventParticipantRepository $eventParticipantRepo */
        $eventParticipantRepo = App::make(EventParticipantRepository::class);
        $theme = $this->fakeEventParticipantData($eventParticipantFields);
        return $eventParticipantRepo->create($theme);
    }

    /**
     * Get fake instance of EventParticipant
     *
     * @param array $eventParticipantFields
     * @return EventParticipant
     */
    public function fakeEventParticipant($eventParticipantFields = [])
    {
        return new EventParticipant($this->fakeEventParticipantData($eventParticipantFields));
    }

    /**
     * Get fake data of EventParticipant
     *
     * @param array $postFields
     * @return array
     */
    public function fakeEventParticipantData($eventParticipantFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'event' => $fake->randomDigitNotNull,
            'participant' => $fake->randomDigitNotNull,
            'type' => $fake->word
        ], $eventParticipantFields);
    }
}
