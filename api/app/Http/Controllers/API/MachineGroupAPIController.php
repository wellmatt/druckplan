<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMachineGroupAPIRequest;
use App\Http\Requests\API\UpdateMachineGroupAPIRequest;
use App\Models\MachineGroup;
use App\Repositories\MachineGroupRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MachineGroupController
 * @package App\Http\Controllers\API
 */

class MachineGroupAPIController extends AppBaseController
{
    /** @var  MachineGroupRepository */
    private $machineGroupRepository;

    public function __construct(MachineGroupRepository $machineGroupRepo)
    {
        $this->machineGroupRepository = $machineGroupRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/machineGroups",
     *      summary="Get a listing of the MachineGroups.",
     *      tags={"MachineGroup"},
     *      description="Get all MachineGroups",
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
     *                  @SWG\Items(ref="#/definitions/MachineGroup")
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
        $this->machineGroupRepository->pushCriteria(new RequestCriteria($request));
        $this->machineGroupRepository->pushCriteria(new LimitOffsetCriteria($request));
        $machineGroups = $this->machineGroupRepository->all();

        return $this->sendResponse($machineGroups->toArray(), 'Machine Groups retrieved successfully');
    }

    /**
     * @param CreateMachineGroupAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/machineGroups",
     *      summary="Store a newly created MachineGroup in storage",
     *      tags={"MachineGroup"},
     *      description="Store MachineGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MachineGroup that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MachineGroup")
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
     *                  ref="#/definitions/MachineGroup"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMachineGroupAPIRequest $request)
    {
        $input = $request->all();

        $machineGroups = $this->machineGroupRepository->create($input);

        return $this->sendResponse($machineGroups->toArray(), 'Machine Group saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/machineGroups/{id}",
     *      summary="Display the specified MachineGroup",
     *      tags={"MachineGroup"},
     *      description="Get MachineGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineGroup",
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
     *                  ref="#/definitions/MachineGroup"
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
        /** @var MachineGroup $machineGroup */
        $machineGroup = $this->machineGroupRepository->find($id);

        if (empty($machineGroup)) {
            return $this->sendError('Machine Group not found');
        }

        return $this->sendResponse($machineGroup->toArray(), 'Machine Group retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMachineGroupAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/machineGroups/{id}",
     *      summary="Update the specified MachineGroup in storage",
     *      tags={"MachineGroup"},
     *      description="Update MachineGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineGroup",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MachineGroup that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MachineGroup")
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
     *                  ref="#/definitions/MachineGroup"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMachineGroupAPIRequest $request)
    {
        $input = $request->all();

        /** @var MachineGroup $machineGroup */
        $machineGroup = $this->machineGroupRepository->find($id);

        if (empty($machineGroup)) {
            return $this->sendError('Machine Group not found');
        }

        $machineGroup = $this->machineGroupRepository->update($input, $id);

        return $this->sendResponse($machineGroup->toArray(), 'MachineGroup updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/machineGroups/{id}",
     *      summary="Remove the specified MachineGroup from storage",
     *      tags={"MachineGroup"},
     *      description="Delete MachineGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineGroup",
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
        /** @var MachineGroup $machineGroup */
        $machineGroup = $this->machineGroupRepository->find($id);

        if (empty($machineGroup)) {
            return $this->sendError('Machine Group not found');
        }

        $machineGroup->delete();

        return $this->sendResponse($id, 'Machine Group deleted successfully');
    }
}
