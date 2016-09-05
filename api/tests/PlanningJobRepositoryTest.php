<?php

use App\Models\PlanningJob;
use App\Repositories\PlanningJobRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PlanningJobRepositoryTest extends TestCase
{
    use MakePlanningJobTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PlanningJobRepository
     */
    protected $planningJobRepo;

    public function setUp()
    {
        parent::setUp();
        $this->planningJobRepo = App::make(PlanningJobRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePlanningJob()
    {
        $planningJob = $this->fakePlanningJobData();
        $createdPlanningJob = $this->planningJobRepo->create($planningJob);
        $createdPlanningJob = $createdPlanningJob->toArray();
        $this->assertArrayHasKey('id', $createdPlanningJob);
        $this->assertNotNull($createdPlanningJob['id'], 'Created PlanningJob must have id specified');
        $this->assertNotNull(PlanningJob::find($createdPlanningJob['id']), 'PlanningJob with given id must be in DB');
        $this->assertModelData($planningJob, $createdPlanningJob);
    }

    /**
     * @test read
     */
    public function testReadPlanningJob()
    {
        $planningJob = $this->makePlanningJob();
        $dbPlanningJob = $this->planningJobRepo->find($planningJob->id);
        $dbPlanningJob = $dbPlanningJob->toArray();
        $this->assertModelData($planningJob->toArray(), $dbPlanningJob);
    }

    /**
     * @test update
     */
    public function testUpdatePlanningJob()
    {
        $planningJob = $this->makePlanningJob();
        $fakePlanningJob = $this->fakePlanningJobData();
        $updatedPlanningJob = $this->planningJobRepo->update($fakePlanningJob, $planningJob->id);
        $this->assertModelData($fakePlanningJob, $updatedPlanningJob->toArray());
        $dbPlanningJob = $this->planningJobRepo->find($planningJob->id);
        $this->assertModelData($fakePlanningJob, $dbPlanningJob->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePlanningJob()
    {
        $planningJob = $this->makePlanningJob();
        $resp = $this->planningJobRepo->delete($planningJob->id);
        $this->assertTrue($resp);
        $this->assertNull(PlanningJob::find($planningJob->id), 'PlanningJob should not exist in DB');
    }
}
