<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePlanningJobAPIRequest;
use App\Http\Requests\API\UpdatePlanningJobAPIRequest;
use App\Models\PlanningJob;
use App\Repositories\PlanningJobRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PlanningJobController
 * @package App\Http\Controllers\API
 */

class PlanningJobAPIController extends AppBaseController
{
    /** @var  PlanningJobRepository */
    private $planningJobRepository;

    public function __construct(PlanningJobRepository $planningJobRepo)
    {
        $this->planningJobRepository = $planningJobRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/planningJobs",
     *      summary="Get a listing of the PlanningJobs.",
     *      tags={"PlanningJob"},
     *      description="Get all PlanningJobs",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/PlanningJob")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->planningJobRepository->pushCriteria(new RequestCriteria($request));
        $this->planningJobRepository->pushCriteria(new LimitOffsetCriteria($request));
        $planningJobs = $this->planningJobRepository->all();

        return $this->sendResponse($planningJobs->toArray(), 'Planning Jobs retrieved successfully');
    }

    /**
     * @param CreatePlanningJobAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/planningJobs",
     *      summary="Store a newly created PlanningJob in storage",
     *      tags={"PlanningJob"},
     *      description="Store PlanningJob",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PlanningJob that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PlanningJob")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/PlanningJob"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePlanningJobAPIRequest $request)
    {
        $input = $request->all();

        $planningJobs = $this->planningJobRepository->create($input);

        return $this->sendResponse($planningJobs->toArray(), 'Planning Job saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/planningJobs/{id}",
     *      summary="Display the specified PlanningJob",
     *      tags={"PlanningJob"},
     *      description="Get PlanningJob",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PlanningJob",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/PlanningJob"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var PlanningJob $planningJob */
        $planningJob = $this->planningJobRepository->find($id);

        if (empty($planningJob)) {
            return $this->sendError('Planning Job not found');
        }

        return $this->sendResponse($planningJob->toArray(), 'Planning Job retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePlanningJobAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/planningJobs/{id}",
     *      summary="Update the specified PlanningJob in storage",
     *      tags={"PlanningJob"},
     *      description="Update PlanningJob",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PlanningJob",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PlanningJob that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PlanningJob")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/PlanningJob"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePlanningJobAPIRequest $request)
    {
        $input = $request->all();

        /** @var PlanningJob $planningJob */
        $planningJob = $this->planningJobRepository->find($id);

        if (empty($planningJob)) {
            return $this->sendError('Planning Job not found');
        }

        $planningJob = $this->planningJobRepository->update($input, $id);

        return $this->sendResponse($planningJob->toArray(), 'PlanningJob updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/planningJobs/{id}",
     *      summary="Remove the specified PlanningJob from storage",
     *      tags={"PlanningJob"},
     *      description="Delete PlanningJob",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PlanningJob",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var PlanningJob $planningJob */
        $planningJob = $this->planningJobRepository->find($id);

        if (empty($planningJob)) {
            return $this->sendError('Planning Job not found');
        }

        $planningJob->delete();

        return $this->sendResponse($id, 'Planning Job deleted successfully');
    }
}
