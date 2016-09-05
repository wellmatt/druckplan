<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStoragePositionAPIRequest;
use App\Http\Requests\API\UpdateStoragePositionAPIRequest;
use App\Models\StoragePosition;
use App\Repositories\StoragePositionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StoragePositionController
 * @package App\Http\Controllers\API
 */

class StoragePositionAPIController extends AppBaseController
{
    /** @var  StoragePositionRepository */
    private $storagePositionRepository;

    public function __construct(StoragePositionRepository $storagePositionRepo)
    {
        $this->storagePositionRepository = $storagePositionRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/storagePositions",
     *      summary="Get a listing of the StoragePositions.",
     *      tags={"StoragePosition"},
     *      description="Get all StoragePositions",
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
     *                  @SWG\Items(ref="#/definitions/StoragePosition")
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
        $this->storagePositionRepository->pushCriteria(new RequestCriteria($request));
        $this->storagePositionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $storagePositions = $this->storagePositionRepository->all();

        return $this->sendResponse($storagePositions->toArray(), 'Storage Positions retrieved successfully');
    }

    /**
     * @param CreateStoragePositionAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/storagePositions",
     *      summary="Store a newly created StoragePosition in storage",
     *      tags={"StoragePosition"},
     *      description="Store StoragePosition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StoragePosition that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StoragePosition")
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
     *                  ref="#/definitions/StoragePosition"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStoragePositionAPIRequest $request)
    {
        $input = $request->all();

        $storagePositions = $this->storagePositionRepository->create($input);

        return $this->sendResponse($storagePositions->toArray(), 'Storage Position saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/storagePositions/{id}",
     *      summary="Display the specified StoragePosition",
     *      tags={"StoragePosition"},
     *      description="Get StoragePosition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StoragePosition",
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
     *                  ref="#/definitions/StoragePosition"
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
        /** @var StoragePosition $storagePosition */
        $storagePosition = $this->storagePositionRepository->find($id);

        if (empty($storagePosition)) {
            return $this->sendError('Storage Position not found');
        }

        return $this->sendResponse($storagePosition->toArray(), 'Storage Position retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateStoragePositionAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/storagePositions/{id}",
     *      summary="Update the specified StoragePosition in storage",
     *      tags={"StoragePosition"},
     *      description="Update StoragePosition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StoragePosition",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StoragePosition that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StoragePosition")
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
     *                  ref="#/definitions/StoragePosition"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStoragePositionAPIRequest $request)
    {
        $input = $request->all();

        /** @var StoragePosition $storagePosition */
        $storagePosition = $this->storagePositionRepository->find($id);

        if (empty($storagePosition)) {
            return $this->sendError('Storage Position not found');
        }

        $storagePosition = $this->storagePositionRepository->update($input, $id);

        return $this->sendResponse($storagePosition->toArray(), 'StoragePosition updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/storagePositions/{id}",
     *      summary="Remove the specified StoragePosition from storage",
     *      tags={"StoragePosition"},
     *      description="Delete StoragePosition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StoragePosition",
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
        /** @var StoragePosition $storagePosition */
        $storagePosition = $this->storagePositionRepository->find($id);

        if (empty($storagePosition)) {
            return $this->sendError('Storage Position not found');
        }

        $storagePosition->delete();

        return $this->sendResponse($id, 'Storage Position deleted successfully');
    }
}
