<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMachineAPIRequest;
use App\Http\Requests\API\UpdateMachineAPIRequest;
use App\Models\Machine;
use App\Repositories\MachineRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MachineController
 * @package App\Http\Controllers\API
 */

class MachineAPIController extends AppBaseController
{
    /** @var  MachineRepository */
    private $machineRepository;

    public function __construct(MachineRepository $machineRepo)
    {
        $this->machineRepository = $machineRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/machines",
     *      summary="Get a listing of the Machines.",
     *      tags={"Machine"},
     *      description="Get all Machines",
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
     *                  @SWG\Items(ref="#/definitions/Machine")
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
        $this->machineRepository->pushCriteria(new RequestCriteria($request));
        $this->machineRepository->pushCriteria(new LimitOffsetCriteria($request));
        $machines = $this->machineRepository->all();

        return $this->sendResponse($machines->toArray(), 'Machines retrieved successfully');
    }

    /**
     * @param CreateMachineAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/machines",
     *      summary="Store a newly created Machine in storage",
     *      tags={"Machine"},
     *      description="Store Machine",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Machine that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Machine")
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
     *                  ref="#/definitions/Machine"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMachineAPIRequest $request)
    {
        $input = $request->all();

        $machines = $this->machineRepository->create($input);

        return $this->sendResponse($machines->toArray(), 'Machine saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/machines/{id}",
     *      summary="Display the specified Machine",
     *      tags={"Machine"},
     *      description="Get Machine",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Machine",
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
     *                  ref="#/definitions/Machine"
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
        /** @var Machine $machine */
        $machine = $this->machineRepository->find($id);

        if (empty($machine)) {
            return $this->sendError('Machine not found');
        }

        return $this->sendResponse($machine->toArray(), 'Machine retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMachineAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/machines/{id}",
     *      summary="Update the specified Machine in storage",
     *      tags={"Machine"},
     *      description="Update Machine",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Machine",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Machine that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Machine")
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
     *                  ref="#/definitions/Machine"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMachineAPIRequest $request)
    {
        $input = $request->all();

        /** @var Machine $machine */
        $machine = $this->machineRepository->find($id);

        if (empty($machine)) {
            return $this->sendError('Machine not found');
        }

        $machine = $this->machineRepository->update($input, $id);

        return $this->sendResponse($machine->toArray(), 'Machine updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/machines/{id}",
     *      summary="Remove the specified Machine from storage",
     *      tags={"Machine"},
     *      description="Delete Machine",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Machine",
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
        /** @var Machine $machine */
        $machine = $this->machineRepository->find($id);

        if (empty($machine)) {
            return $this->sendError('Machine not found');
        }

        $machine->delete();

        return $this->sendResponse($id, 'Machine deleted successfully');
    }
}
