<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMachineUnitPerHourAPIRequest;
use App\Http\Requests\API\UpdateMachineUnitPerHourAPIRequest;
use App\Models\MachineUnitPerHour;
use App\Repositories\MachineUnitPerHourRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MachineUnitPerHourController
 * @package App\Http\Controllers\API
 */

class MachineUnitPerHourAPIController extends AppBaseController
{
    /** @var  MachineUnitPerHourRepository */
    private $machineUnitPerHourRepository;

    public function __construct(MachineUnitPerHourRepository $machineUnitPerHourRepo)
    {
        $this->machineUnitPerHourRepository = $machineUnitPerHourRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/machineUnitPerHours",
     *      summary="Get a listing of the MachineUnitPerHours.",
     *      tags={"MachineUnitPerHour"},
     *      description="Get all MachineUnitPerHours",
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
     *                  @SWG\Items(ref="#/definitions/MachineUnitPerHour")
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
        $this->machineUnitPerHourRepository->pushCriteria(new RequestCriteria($request));
        $this->machineUnitPerHourRepository->pushCriteria(new LimitOffsetCriteria($request));
        $machineUnitPerHours = $this->machineUnitPerHourRepository->all();

        return $this->sendResponse($machineUnitPerHours->toArray(), 'Machine Unit Per Hours retrieved successfully');
    }

    /**
     * @param CreateMachineUnitPerHourAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/machineUnitPerHours",
     *      summary="Store a newly created MachineUnitPerHour in storage",
     *      tags={"MachineUnitPerHour"},
     *      description="Store MachineUnitPerHour",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MachineUnitPerHour that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MachineUnitPerHour")
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
     *                  ref="#/definitions/MachineUnitPerHour"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMachineUnitPerHourAPIRequest $request)
    {
        $input = $request->all();

        $machineUnitPerHours = $this->machineUnitPerHourRepository->create($input);

        return $this->sendResponse($machineUnitPerHours->toArray(), 'Machine Unit Per Hour saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/machineUnitPerHours/{id}",
     *      summary="Display the specified MachineUnitPerHour",
     *      tags={"MachineUnitPerHour"},
     *      description="Get MachineUnitPerHour",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineUnitPerHour",
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
     *                  ref="#/definitions/MachineUnitPerHour"
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
        /** @var MachineUnitPerHour $machineUnitPerHour */
        $machineUnitPerHour = $this->machineUnitPerHourRepository->find($id);

        if (empty($machineUnitPerHour)) {
            return $this->sendError('Machine Unit Per Hour not found');
        }

        return $this->sendResponse($machineUnitPerHour->toArray(), 'Machine Unit Per Hour retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMachineUnitPerHourAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/machineUnitPerHours/{id}",
     *      summary="Update the specified MachineUnitPerHour in storage",
     *      tags={"MachineUnitPerHour"},
     *      description="Update MachineUnitPerHour",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineUnitPerHour",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MachineUnitPerHour that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MachineUnitPerHour")
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
     *                  ref="#/definitions/MachineUnitPerHour"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMachineUnitPerHourAPIRequest $request)
    {
        $input = $request->all();

        /** @var MachineUnitPerHour $machineUnitPerHour */
        $machineUnitPerHour = $this->machineUnitPerHourRepository->find($id);

        if (empty($machineUnitPerHour)) {
            return $this->sendError('Machine Unit Per Hour not found');
        }

        $machineUnitPerHour = $this->machineUnitPerHourRepository->update($input, $id);

        return $this->sendResponse($machineUnitPerHour->toArray(), 'MachineUnitPerHour updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/machineUnitPerHours/{id}",
     *      summary="Remove the specified MachineUnitPerHour from storage",
     *      tags={"MachineUnitPerHour"},
     *      description="Delete MachineUnitPerHour",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineUnitPerHour",
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
        /** @var MachineUnitPerHour $machineUnitPerHour */
        $machineUnitPerHour = $this->machineUnitPerHourRepository->find($id);

        if (empty($machineUnitPerHour)) {
            return $this->sendError('Machine Unit Per Hour not found');
        }

        $machineUnitPerHour->delete();

        return $this->sendResponse($id, 'Machine Unit Per Hour deleted successfully');
    }
}
