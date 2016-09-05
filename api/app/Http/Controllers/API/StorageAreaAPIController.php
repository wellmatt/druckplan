<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStorageAreaAPIRequest;
use App\Http\Requests\API\UpdateStorageAreaAPIRequest;
use App\Models\StorageArea;
use App\Repositories\StorageAreaRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StorageAreaController
 * @package App\Http\Controllers\API
 */

class StorageAreaAPIController extends AppBaseController
{
    /** @var  StorageAreaRepository */
    private $storageAreaRepository;

    public function __construct(StorageAreaRepository $storageAreaRepo)
    {
        $this->storageAreaRepository = $storageAreaRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/storageAreas",
     *      summary="Get a listing of the StorageAreas.",
     *      tags={"StorageArea"},
     *      description="Get all StorageAreas",
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
     *                  @SWG\Items(ref="#/definitions/StorageArea")
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
        $this->storageAreaRepository->pushCriteria(new RequestCriteria($request));
        $this->storageAreaRepository->pushCriteria(new LimitOffsetCriteria($request));
        $storageAreas = $this->storageAreaRepository->all();

        return $this->sendResponse($storageAreas->toArray(), 'Storage Areas retrieved successfully');
    }

    /**
     * @param CreateStorageAreaAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/storageAreas",
     *      summary="Store a newly created StorageArea in storage",
     *      tags={"StorageArea"},
     *      description="Store StorageArea",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StorageArea that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StorageArea")
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
     *                  ref="#/definitions/StorageArea"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStorageAreaAPIRequest $request)
    {
        $input = $request->all();

        $storageAreas = $this->storageAreaRepository->create($input);

        return $this->sendResponse($storageAreas->toArray(), 'Storage Area saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/storageAreas/{id}",
     *      summary="Display the specified StorageArea",
     *      tags={"StorageArea"},
     *      description="Get StorageArea",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StorageArea",
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
     *                  ref="#/definitions/StorageArea"
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
        /** @var StorageArea $storageArea */
        $storageArea = $this->storageAreaRepository->find($id);

        if (empty($storageArea)) {
            return $this->sendError('Storage Area not found');
        }

        return $this->sendResponse($storageArea->toArray(), 'Storage Area retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateStorageAreaAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/storageAreas/{id}",
     *      summary="Update the specified StorageArea in storage",
     *      tags={"StorageArea"},
     *      description="Update StorageArea",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StorageArea",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StorageArea that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StorageArea")
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
     *                  ref="#/definitions/StorageArea"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStorageAreaAPIRequest $request)
    {
        $input = $request->all();

        /** @var StorageArea $storageArea */
        $storageArea = $this->storageAreaRepository->find($id);

        if (empty($storageArea)) {
            return $this->sendError('Storage Area not found');
        }

        $storageArea = $this->storageAreaRepository->update($input, $id);

        return $this->sendResponse($storageArea->toArray(), 'StorageArea updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/storageAreas/{id}",
     *      summary="Remove the specified StorageArea from storage",
     *      tags={"StorageArea"},
     *      description="Delete StorageArea",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StorageArea",
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
        /** @var StorageArea $storageArea */
        $storageArea = $this->storageAreaRepository->find($id);

        if (empty($storageArea)) {
            return $this->sendError('Storage Area not found');
        }

        $storageArea->delete();

        return $this->sendResponse($id, 'Storage Area deleted successfully');
    }
}
