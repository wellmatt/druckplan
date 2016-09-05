<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMachineQualifiedUserAPIRequest;
use App\Http\Requests\API\UpdateMachineQualifiedUserAPIRequest;
use App\Models\MachineQualifiedUser;
use App\Repositories\MachineQualifiedUserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MachineQualifiedUserController
 * @package App\Http\Controllers\API
 */

class MachineQualifiedUserAPIController extends AppBaseController
{
    /** @var  MachineQualifiedUserRepository */
    private $machineQualifiedUserRepository;

    public function __construct(MachineQualifiedUserRepository $machineQualifiedUserRepo)
    {
        $this->machineQualifiedUserRepository = $machineQualifiedUserRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/machineQualifiedUsers",
     *      summary="Get a listing of the MachineQualifiedUsers.",
     *      tags={"MachineQualifiedUser"},
     *      description="Get all MachineQualifiedUsers",
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
     *                  @SWG\Items(ref="#/definitions/MachineQualifiedUser")
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
        $this->machineQualifiedUserRepository->pushCriteria(new RequestCriteria($request));
        $this->machineQualifiedUserRepository->pushCriteria(new LimitOffsetCriteria($request));
        $machineQualifiedUsers = $this->machineQualifiedUserRepository->all();

        return $this->sendResponse($machineQualifiedUsers->toArray(), 'Machine Qualified Users retrieved successfully');
    }

    /**
     * @param CreateMachineQualifiedUserAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/machineQualifiedUsers",
     *      summary="Store a newly created MachineQualifiedUser in storage",
     *      tags={"MachineQualifiedUser"},
     *      description="Store MachineQualifiedUser",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MachineQualifiedUser that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MachineQualifiedUser")
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
     *                  ref="#/definitions/MachineQualifiedUser"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMachineQualifiedUserAPIRequest $request)
    {
        $input = $request->all();

        $machineQualifiedUsers = $this->machineQualifiedUserRepository->create($input);

        return $this->sendResponse($machineQualifiedUsers->toArray(), 'Machine Qualified User saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/machineQualifiedUsers/{id}",
     *      summary="Display the specified MachineQualifiedUser",
     *      tags={"MachineQualifiedUser"},
     *      description="Get MachineQualifiedUser",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineQualifiedUser",
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
     *                  ref="#/definitions/MachineQualifiedUser"
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
        /** @var MachineQualifiedUser $machineQualifiedUser */
        $machineQualifiedUser = $this->machineQualifiedUserRepository->find($id);

        if (empty($machineQualifiedUser)) {
            return $this->sendError('Machine Qualified User not found');
        }

        return $this->sendResponse($machineQualifiedUser->toArray(), 'Machine Qualified User retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMachineQualifiedUserAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/machineQualifiedUsers/{id}",
     *      summary="Update the specified MachineQualifiedUser in storage",
     *      tags={"MachineQualifiedUser"},
     *      description="Update MachineQualifiedUser",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineQualifiedUser",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MachineQualifiedUser that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MachineQualifiedUser")
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
     *                  ref="#/definitions/MachineQualifiedUser"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMachineQualifiedUserAPIRequest $request)
    {
        $input = $request->all();

        /** @var MachineQualifiedUser $machineQualifiedUser */
        $machineQualifiedUser = $this->machineQualifiedUserRepository->find($id);

        if (empty($machineQualifiedUser)) {
            return $this->sendError('Machine Qualified User not found');
        }

        $machineQualifiedUser = $this->machineQualifiedUserRepository->update($input, $id);

        return $this->sendResponse($machineQualifiedUser->toArray(), 'MachineQualifiedUser updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/machineQualifiedUsers/{id}",
     *      summary="Remove the specified MachineQualifiedUser from storage",
     *      tags={"MachineQualifiedUser"},
     *      description="Delete MachineQualifiedUser",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineQualifiedUser",
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
        /** @var MachineQualifiedUser $machineQualifiedUser */
        $machineQualifiedUser = $this->machineQualifiedUserRepository->find($id);

        if (empty($machineQualifiedUser)) {
            return $this->sendError('Machine Qualified User not found');
        }

        $machineQualifiedUser->delete();

        return $this->sendResponse($id, 'Machine Qualified User deleted successfully');
    }
}
