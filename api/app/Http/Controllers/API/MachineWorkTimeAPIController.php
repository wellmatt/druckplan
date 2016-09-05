<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMachineWorkTimeAPIRequest;
use App\Http\Requests\API\UpdateMachineWorkTimeAPIRequest;
use App\Models\MachineWorkTime;
use App\Repositories\MachineWorkTimeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MachineWorkTimeController
 * @package App\Http\Controllers\API
 */

class MachineWorkTimeAPIController extends AppBaseController
{
    /** @var  MachineWorkTimeRepository */
    private $machineWorkTimeRepository;

    public function __construct(MachineWorkTimeRepository $machineWorkTimeRepo)
    {
        $this->machineWorkTimeRepository = $machineWorkTimeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/machineWorkTimes",
     *      summary="Get a listing of the MachineWorkTimes.",
     *      tags={"MachineWorkTime"},
     *      description="Get all MachineWorkTimes",
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
     *                  @SWG\Items(ref="#/definitions/MachineWorkTime")
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
        $this->machineWorkTimeRepository->pushCriteria(new RequestCriteria($request));
        $this->machineWorkTimeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $machineWorkTimes = $this->machineWorkTimeRepository->all();

        return $this->sendResponse($machineWorkTimes->toArray(), 'Machine Work Times retrieved successfully');
    }

    /**
     * @param CreateMachineWorkTimeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/machineWorkTimes",
     *      summary="Store a newly created MachineWorkTime in storage",
     *      tags={"MachineWorkTime"},
     *      description="Store MachineWorkTime",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MachineWorkTime that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MachineWorkTime")
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
     *                  ref="#/definitions/MachineWorkTime"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMachineWorkTimeAPIRequest $request)
    {
        $input = $request->all();

        $machineWorkTimes = $this->machineWorkTimeRepository->create($input);

        return $this->sendResponse($machineWorkTimes->toArray(), 'Machine Work Time saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/machineWorkTimes/{id}",
     *      summary="Display the specified MachineWorkTime",
     *      tags={"MachineWorkTime"},
     *      description="Get MachineWorkTime",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineWorkTime",
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
     *                  ref="#/definitions/MachineWorkTime"
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
        /** @var MachineWorkTime $machineWorkTime */
        $machineWorkTime = $this->machineWorkTimeRepository->find($id);

        if (empty($machineWorkTime)) {
            return $this->sendError('Machine Work Time not found');
        }

        return $this->sendResponse($machineWorkTime->toArray(), 'Machine Work Time retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMachineWorkTimeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/machineWorkTimes/{id}",
     *      summary="Update the specified MachineWorkTime in storage",
     *      tags={"MachineWorkTime"},
     *      description="Update MachineWorkTime",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineWorkTime",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MachineWorkTime that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MachineWorkTime")
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
     *                  ref="#/definitions/MachineWorkTime"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMachineWorkTimeAPIRequest $request)
    {
        $input = $request->all();

        /** @var MachineWorkTime $machineWorkTime */
        $machineWorkTime = $this->machineWorkTimeRepository->find($id);

        if (empty($machineWorkTime)) {
            return $this->sendError('Machine Work Time not found');
        }

        $machineWorkTime = $this->machineWorkTimeRepository->update($input, $id);

        return $this->sendResponse($machineWorkTime->toArray(), 'MachineWorkTime updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/machineWorkTimes/{id}",
     *      summary="Remove the specified MachineWorkTime from storage",
     *      tags={"MachineWorkTime"},
     *      description="Delete MachineWorkTime",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineWorkTime",
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
        /** @var MachineWorkTime $machineWorkTime */
        $machineWorkTime = $this->machineWorkTimeRepository->find($id);

        if (empty($machineWorkTime)) {
            return $this->sendError('Machine Work Time not found');
        }

        $machineWorkTime->delete();

        return $this->sendResponse($id, 'Machine Work Time deleted successfully');
    }
}
