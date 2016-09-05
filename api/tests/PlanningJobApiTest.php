<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PlanningJobApiTest extends TestCase
{
    use MakePlanningJobTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePlanningJob()
    {
        $planningJob = $this->fakePlanningJobData();
        $this->json('POST', '/api/v1/planningJobs', $planningJob);

        $this->assertApiResponse($planningJob);
    }

    /**
     * @test
     */
    public function testReadPlanningJob()
    {
        $planningJob = $this->makePlanningJob();
        $this->json('GET', '/api/v1/planningJobs/'.$planningJob->id);

        $this->assertApiResponse($planningJob->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePlanningJob()
    {
        $planningJob = $this->makePlanningJob();
        $editedPlanningJob = $this->fakePlanningJobData();

        $this->json('PUT', '/api/v1/planningJobs/'.$planningJob->id, $editedPlanningJob);

        $this->assertApiResponse($editedPlanningJob);
    }

    /**
     * @test
     */
    public function testDeletePlanningJob()
    {
        $planningJob = $this->makePlanningJob();
        $this->json('DELETE', '/api/v1/planningJobs/'.$planningJob->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/planningJobs/'.$planningJob->id);

        $this->assertResponseStatus(404);
    }
}
