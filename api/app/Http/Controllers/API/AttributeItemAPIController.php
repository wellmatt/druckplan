<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAttributeItemAPIRequest;
use App\Http\Requests\API\UpdateAttributeItemAPIRequest;
use App\Models\AttributeItem;
use App\Repositories\AttributeItemRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AttributeItemController
 * @package App\Http\Controllers\API
 */

class AttributeItemAPIController extends AppBaseController
{
    /** @var  AttributeItemRepository */
    private $attributeItemRepository;

    public function __construct(AttributeItemRepository $attributeItemRepo)
    {
        $this->attributeItemRepository = $attributeItemRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/attributeItems",
     *      summary="Get a listing of the AttributeItems.",
     *      tags={"AttributeItem"},
     *      description="Get all AttributeItems",
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
     *                  @SWG\Items(ref="#/definitions/AttributeItem")
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
        $this->attributeItemRepository->pushCriteria(new RequestCriteria($request));
        $this->attributeItemRepository->pushCriteria(new LimitOffsetCriteria($request));
        $attributeItems = $this->attributeItemRepository->all();

        return $this->sendResponse($attributeItems->toArray(), 'Attribute Items retrieved successfully');
    }

    /**
     * @param CreateAttributeItemAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/attributeItems",
     *      summary="Store a newly created AttributeItem in storage",
     *      tags={"AttributeItem"},
     *      description="Store AttributeItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AttributeItem that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AttributeItem")
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
     *                  ref="#/definitions/AttributeItem"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAttributeItemAPIRequest $request)
    {
        $input = $request->all();

        $attributeItems = $this->attributeItemRepository->create($input);

        return $this->sendResponse($attributeItems->toArray(), 'Attribute Item saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/attributeItems/{id}",
     *      summary="Display the specified AttributeItem",
     *      tags={"AttributeItem"},
     *      description="Get AttributeItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AttributeItem",
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
     *                  ref="#/definitions/AttributeItem"
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
        /** @var AttributeItem $attributeItem */
        $attributeItem = $this->attributeItemRepository->find($id);

        if (empty($attributeItem)) {
            return $this->sendError('Attribute Item not found');
        }

        return $this->sendResponse($attributeItem->toArray(), 'Attribute Item retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAttributeItemAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/attributeItems/{id}",
     *      summary="Update the specified AttributeItem in storage",
     *      tags={"AttributeItem"},
     *      description="Update AttributeItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AttributeItem",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AttributeItem that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AttributeItem")
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
     *                  ref="#/definitions/AttributeItem"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAttributeItemAPIRequest $request)
    {
        $input = $request->all();

        /** @var AttributeItem $attributeItem */
        $attributeItem = $this->attributeItemRepository->find($id);

        if (empty($attributeItem)) {
            return $this->sendError('Attribute Item not found');
        }

        $attributeItem = $this->attributeItemRepository->update($input, $id);

        return $this->sendResponse($attributeItem->toArray(), 'AttributeItem updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/attributeItems/{id}",
     *      summary="Remove the specified AttributeItem from storage",
     *      tags={"AttributeItem"},
     *      description="Delete AttributeItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AttributeItem",
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
        /** @var AttributeItem $attributeItem */
        $attributeItem = $this->attributeItemRepository->find($id);

        if (empty($attributeItem)) {
            return $this->sendError('Attribute Item not found');
        }

        $attributeItem->delete();

        return $this->sendResponse($id, 'Attribute Item deleted successfully');
    }
}
