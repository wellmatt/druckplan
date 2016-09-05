<?php

use Faker\Factory as Faker;
use App\Models\PlanningJob;
use App\Repositories\PlanningJobRepository;

trait MakePlanningJobTrait
{
    /**
     * Create fake instance of PlanningJob and save it in database
     *
     * @param array $planningJobFields
     * @return PlanningJob
     */
    public function makePlanningJob($planningJobFields = [])
    {
        /** @var PlanningJobRepository $planningJobRepo */
        $planningJobRepo = App::make(PlanningJobRepository::class);
        $theme = $this->fakePlanningJobData($planningJobFields);
        return $planningJobRepo->create($theme);
    }

    /**
     * Get fake instance of PlanningJob
     *
     * @param array $planningJobFields
     * @return PlanningJob
     */
    public function fakePlanningJob($planningJobFields = [])
    {
        return new PlanningJob($this->fakePlanningJobData($planningJobFields));
    }

    /**
     * Get fake data of PlanningJob
     *
     * @param array $postFields
     * @return array
     */
    public function fakePlanningJobData($planningJobFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'object' => $fake->randomDigitNotNull,
            'type' => $fake->word,
            'opos' => $fake->randomDigitNotNull,
            'subobject' => $fake->randomDigitNotNull,
            'assigned_user' => $fake->randomDigitNotNull,
            'assigned_group' => $fake->randomDigitNotNull,
            'ticket' => $fake->randomDigitNotNull,
            'start' => $fake->randomDigitNotNull,
            'tplanned' => $fake->randomDigitNotNull,
            'tactual' => $fake->randomDigitNotNull,
            'state' => $fake->word,
            'artmach' => $fake->randomDigitNotNull
        ], $planningJobFields);
    }
}
