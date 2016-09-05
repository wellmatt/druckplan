<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStorageGoodPositionAPIRequest;
use App\Http\Requests\API\UpdateStorageGoodPositionAPIRequest;
use App\Models\StorageGoodPosition;
use App\Repositories\StorageGoodPositionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StorageGoodPositionController
 * @package App\Http\Controllers\API
 */

class StorageGoodPositionAPIController extends AppBaseController
{
    /** @var  StorageGoodPositionRepository */
    private $storageGoodPositionRepository;

    public function __construct(StorageGoodPositionRepository $storageGoodPositionRepo)
    {
        $this->storageGoodPositionRepository = $storageGoodPositionRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/storageGoodPositions",
     *      summary="Get a listing of the StorageGoodPositions.",
     *      tags={"StorageGoodPosition"},
     *      description="Get all StorageGoodPositions",
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
     *                  @SWG\Items(ref="#/definitions/StorageGoodPosition")
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
        $this->storageGoodPositionRepository->pushCriteria(new RequestCriteria($request));
        $this->storageGoodPositionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $storageGoodPositions = $this->storageGoodPositionRepository->all();

        return $this->sendResponse($storageGoodPositions->toArray(), 'Storage Good Positions retrieved successfully');
    }

    /**
     * @param CreateStorageGoodPositionAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/storageGoodPositions",
     *      summary="Store a newly created StorageGoodPosition in storage",
     *      tags={"StorageGoodPosition"},
     *      description="Store StorageGoodPosition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StorageGoodPosition that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StorageGoodPosition")
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
     *                  ref="#/definitions/StorageGoodPosition"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStorageGoodPositionAPIRequest $request)
    {
        $input = $request->all();

        $storageGoodPositions = $this->storageGoodPositionRepository->create($input);

        return $this->sendResponse($storageGoodPositions->toArray(), 'Storage Good Position saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/storageGoodPositions/{id}",
     *      summary="Display the specified StorageGoodPosition",
     *      tags={"StorageGoodPosition"},
     *      description="Get StorageGoodPosition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StorageGoodPosition",
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
     *                  ref="#/definitions/StorageGoodPosition"
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
        /** @var StorageGoodPosition $storageGoodPosition */
        $storageGoodPosition = $this->storageGoodPositionRepository->find($id);

        if (empty($storageGoodPosition)) {
            return $this->sendError('Storage Good Position not found');
        }

        return $this->sendResponse($storageGoodPosition->toArray(), 'Storage Good Position retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateStorageGoodPositionAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/storageGoodPositions/{id}",
     *      summary="Update the specified StorageGoodPosition in storage",
     *      tags={"StorageGoodPosition"},
     *      description="Update StorageGoodPosition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StorageGoodPosition",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StorageGoodPosition that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StorageGoodPosition")
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
     *                  ref="#/definitions/StorageGoodPosition"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStorageGoodPositionAPIRequest $request)
    {
        $input = $request->all();

        /** @var StorageGoodPosition $storageGoodPosition */
        $storageGoodPosition = $this->storageGoodPositionRepository->find($id);

        if (empty($storageGoodPosition)) {
            return $this->sendError('Storage Good Position not found');
        }

        $storageGoodPosition = $this->storageGoodPositionRepository->update($input, $id);

        return $this->sendResponse($storageGoodPosition->toArray(), 'StorageGoodPosition updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/storageGoodPositions/{id}",
     *      summary="Remove the specified StorageGoodPosition from storage",
     *      tags={"StorageGoodPosition"},
     *      description="Delete StorageGoodPosition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StorageGoodPosition",
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
        /** @var StorageGoodPosition $storageGoodPosition */
        $storageGoodPosition = $this->storageGoodPositionRepository->find($id);

        if (empty($storageGoodPosition)) {
            return $this->sendError('Storage Good Position not found');
        }

        $storageGoodPosition->delete();

        return $this->sendResponse($id, 'Storage Good Position deleted successfully');
    }
}
