<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMachineLockAPIRequest;
use App\Http\Requests\API\UpdateMachineLockAPIRequest;
use App\Models\MachineLock;
use App\Repositories\MachineLockRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MachineLockController
 * @package App\Http\Controllers\API
 */

class MachineLockAPIController extends AppBaseController
{
    /** @var  MachineLockRepository */
    private $machineLockRepository;

    public function __construct(MachineLockRepository $machineLockRepo)
    {
        $this->machineLockRepository = $machineLockRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/machineLocks",
     *      summary="Get a listing of the MachineLocks.",
     *      tags={"MachineLock"},
     *      description="Get all MachineLocks",
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
     *                  @SWG\Items(ref="#/definitions/MachineLock")
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
        $this->machineLockRepository->pushCriteria(new RequestCriteria($request));
        $this->machineLockRepository->pushCriteria(new LimitOffsetCriteria($request));
        $machineLocks = $this->machineLockRepository->all();

        return $this->sendResponse($machineLocks->toArray(), 'Machine Locks retrieved successfully');
    }

    /**
     * @param CreateMachineLockAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/machineLocks",
     *      summary="Store a newly created MachineLock in storage",
     *      tags={"MachineLock"},
     *      description="Store MachineLock",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MachineLock that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MachineLock")
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
     *                  ref="#/definitions/MachineLock"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMachineLockAPIRequest $request)
    {
        $input = $request->all();

        $machineLocks = $this->machineLockRepository->create($input);

        return $this->sendResponse($machineLocks->toArray(), 'Machine Lock saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/machineLocks/{id}",
     *      summary="Display the specified MachineLock",
     *      tags={"MachineLock"},
     *      description="Get MachineLock",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineLock",
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
     *                  ref="#/definitions/MachineLock"
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
        /** @var MachineLock $machineLock */
        $machineLock = $this->machineLockRepository->find($id);

        if (empty($machineLock)) {
            return $this->sendError('Machine Lock not found');
        }

        return $this->sendResponse($machineLock->toArray(), 'Machine Lock retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMachineLockAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/machineLocks/{id}",
     *      summary="Update the specified MachineLock in storage",
     *      tags={"MachineLock"},
     *      description="Update MachineLock",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineLock",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MachineLock that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MachineLock")
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
     *                  ref="#/definitions/MachineLock"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMachineLockAPIRequest $request)
    {
        $input = $request->all();

        /** @var MachineLock $machineLock */
        $machineLock = $this->machineLockRepository->find($id);

        if (empty($machineLock)) {
            return $this->sendError('Machine Lock not found');
        }

        $machineLock = $this->machineLockRepository->update($input, $id);

        return $this->sendResponse($machineLock->toArray(), 'MachineLock updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/machineLocks/{id}",
     *      summary="Remove the specified MachineLock from storage",
     *      tags={"MachineLock"},
     *      description="Delete MachineLock",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineLock",
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
        /** @var MachineLock $machineLock */
        $machineLock = $this->machineLockRepository->find($id);

        if (empty($machineLock)) {
            return $this->sendError('Machine Lock not found');
        }

        $machineLock->delete();

        return $this->sendResponse($id, 'Machine Lock deleted successfully');
    }
}
