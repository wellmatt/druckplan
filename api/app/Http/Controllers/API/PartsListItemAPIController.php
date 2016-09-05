<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePartsListItemAPIRequest;
use App\Http\Requests\API\UpdatePartsListItemAPIRequest;
use App\Models\PartsListItem;
use App\Repositories\PartsListItemRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PartsListItemController
 * @package App\Http\Controllers\API
 */

class PartsListItemAPIController extends AppBaseController
{
    /** @var  PartsListItemRepository */
    private $partsListItemRepository;

    public function __construct(PartsListItemRepository $partsListItemRepo)
    {
        $this->partsListItemRepository = $partsListItemRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/partsListItems",
     *      summary="Get a listing of the PartsListItems.",
     *      tags={"PartsListItem"},
     *      description="Get all PartsListItems",
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
     *                  @SWG\Items(ref="#/definitions/PartsListItem")
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
        $this->partsListItemRepository->pushCriteria(new RequestCriteria($request));
        $this->partsListItemRepository->pushCriteria(new LimitOffsetCriteria($request));
        $partsListItems = $this->partsListItemRepository->all();

        return $this->sendResponse($partsListItems->toArray(), 'Parts List Items retrieved successfully');
    }

    /**
     * @param CreatePartsListItemAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/partsListItems",
     *      summary="Store a newly created PartsListItem in storage",
     *      tags={"PartsListItem"},
     *      description="Store PartsListItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PartsListItem that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PartsListItem")
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
     *                  ref="#/definitions/PartsListItem"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePartsListItemAPIRequest $request)
    {
        $input = $request->all();

        $partsListItems = $this->partsListItemRepository->create($input);

        return $this->sendResponse($partsListItems->toArray(), 'Parts List Item saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/partsListItems/{id}",
     *      summary="Display the specified PartsListItem",
     *      tags={"PartsListItem"},
     *      description="Get PartsListItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PartsListItem",
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
     *                  ref="#/definitions/PartsListItem"
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
        /** @var PartsListItem $partsListItem */
        $partsListItem = $this->partsListItemRepository->find($id);

        if (empty($partsListItem)) {
            return $this->sendError('Parts List Item not found');
        }

        return $this->sendResponse($partsListItem->toArray(), 'Parts List Item retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePartsListItemAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/partsListItems/{id}",
     *      summary="Update the specified PartsListItem in storage",
     *      tags={"PartsListItem"},
     *      description="Update PartsListItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PartsListItem",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PartsListItem that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PartsListItem")
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
     *                  ref="#/definitions/PartsListItem"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePartsListItemAPIRequest $request)
    {
        $input = $request->all();

        /** @var PartsListItem $partsListItem */
        $partsListItem = $this->partsListItemRepository->find($id);

        if (empty($partsListItem)) {
            return $this->sendError('Parts List Item not found');
        }

        $partsListItem = $this->partsListItemRepository->update($input, $id);

        return $this->sendResponse($partsListItem->toArray(), 'PartsListItem updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/partsListItems/{id}",
     *      summary="Remove the specified PartsListItem from storage",
     *      tags={"PartsListItem"},
     *      description="Delete PartsListItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PartsListItem",
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
        /** @var PartsListItem $partsListItem */
        $partsListItem = $this->partsListItemRepository->find($id);

        if (empty($partsListItem)) {
            return $this->sendError('Parts List Item not found');
        }

        $partsListItem->delete();

        return $this->sendResponse($id, 'Parts List Item deleted successfully');
    }
}
