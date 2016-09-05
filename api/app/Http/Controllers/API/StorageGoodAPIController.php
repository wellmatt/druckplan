<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStorageGoodAPIRequest;
use App\Http\Requests\API\UpdateStorageGoodAPIRequest;
use App\Models\StorageGood;
use App\Repositories\StorageGoodRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StorageGoodController
 * @package App\Http\Controllers\API
 */

class StorageGoodAPIController extends AppBaseController
{
    /** @var  StorageGoodRepository */
    private $storageGoodRepository;

    public function __construct(StorageGoodRepository $storageGoodRepo)
    {
        $this->storageGoodRepository = $storageGoodRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/storageGoods",
     *      summary="Get a listing of the StorageGoods.",
     *      tags={"StorageGood"},
     *      description="Get all StorageGoods",
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
     *                  @SWG\Items(ref="#/definitions/StorageGood")
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
        $this->storageGoodRepository->pushCriteria(new RequestCriteria($request));
        $this->storageGoodRepository->pushCriteria(new LimitOffsetCriteria($request));
        $storageGoods = $this->storageGoodRepository->all();

        return $this->sendResponse($storageGoods->toArray(), 'Storage Goods retrieved successfully');
    }

    /**
     * @param CreateStorageGoodAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/storageGoods",
     *      summary="Store a newly created StorageGood in storage",
     *      tags={"StorageGood"},
     *      description="Store StorageGood",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StorageGood that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StorageGood")
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
     *                  ref="#/definitions/StorageGood"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStorageGoodAPIRequest $request)
    {
        $input = $request->all();

        $storageGoods = $this->storageGoodRepository->create($input);

        return $this->sendResponse($storageGoods->toArray(), 'Storage Good saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/storageGoods/{id}",
     *      summary="Display the specified StorageGood",
     *      tags={"StorageGood"},
     *      description="Get StorageGood",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StorageGood",
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
     *                  ref="#/definitions/StorageGood"
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
        /** @var StorageGood $storageGood */
        $storageGood = $this->storageGoodRepository->find($id);

        if (empty($storageGood)) {
            return $this->sendError('Storage Good not found');
        }

        return $this->sendResponse($storageGood->toArray(), 'Storage Good retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateStorageGoodAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/storageGoods/{id}",
     *      summary="Update the specified StorageGood in storage",
     *      tags={"StorageGood"},
     *      description="Update StorageGood",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StorageGood",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StorageGood that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StorageGood")
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
     *                  ref="#/definitions/StorageGood"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStorageGoodAPIRequest $request)
    {
        $input = $request->all();

        /** @var StorageGood $storageGood */
        $storageGood = $this->storageGoodRepository->find($id);

        if (empty($storageGood)) {
            return $this->sendError('Storage Good not found');
        }

        $storageGood = $this->storageGoodRepository->update($input, $id);

        return $this->sendResponse($storageGood->toArray(), 'StorageGood updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/storageGoods/{id}",
     *      summary="Remove the specified StorageGood from storage",
     *      tags={"StorageGood"},
     *      description="Delete StorageGood",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StorageGood",
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
        /** @var StorageGood $storageGood */
        $storageGood = $this->storageGoodRepository->find($id);

        if (empty($storageGood)) {
            return $this->sendError('Storage Good not found');
        }

        $storageGood->delete();

        return $this->sendResponse($id, 'Storage Good deleted successfully');
    }
}
