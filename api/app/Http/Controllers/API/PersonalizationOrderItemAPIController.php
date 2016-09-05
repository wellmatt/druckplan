<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePersonalizationOrderItemAPIRequest;
use App\Http\Requests\API\UpdatePersonalizationOrderItemAPIRequest;
use App\Models\PersonalizationOrderItem;
use App\Repositories\PersonalizationOrderItemRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PersonalizationOrderItemController
 * @package App\Http\Controllers\API
 */

class PersonalizationOrderItemAPIController extends AppBaseController
{
    /** @var  PersonalizationOrderItemRepository */
    private $personalizationOrderItemRepository;

    public function __construct(PersonalizationOrderItemRepository $personalizationOrderItemRepo)
    {
        $this->personalizationOrderItemRepository = $personalizationOrderItemRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/personalizationOrderItems",
     *      summary="Get a listing of the PersonalizationOrderItems.",
     *      tags={"PersonalizationOrderItem"},
     *      description="Get all PersonalizationOrderItems",
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
     *                  @SWG\Items(ref="#/definitions/PersonalizationOrderItem")
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
        $this->personalizationOrderItemRepository->pushCriteria(new RequestCriteria($request));
        $this->personalizationOrderItemRepository->pushCriteria(new LimitOffsetCriteria($request));
        $personalizationOrderItems = $this->personalizationOrderItemRepository->all();

        return $this->sendResponse($personalizationOrderItems->toArray(), 'Personalization Order Items retrieved successfully');
    }

    /**
     * @param CreatePersonalizationOrderItemAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/personalizationOrderItems",
     *      summary="Store a newly created PersonalizationOrderItem in storage",
     *      tags={"PersonalizationOrderItem"},
     *      description="Store PersonalizationOrderItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PersonalizationOrderItem that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PersonalizationOrderItem")
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
     *                  ref="#/definitions/PersonalizationOrderItem"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePersonalizationOrderItemAPIRequest $request)
    {
        $input = $request->all();

        $personalizationOrderItems = $this->personalizationOrderItemRepository->create($input);

        return $this->sendResponse($personalizationOrderItems->toArray(), 'Personalization Order Item saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/personalizationOrderItems/{id}",
     *      summary="Display the specified PersonalizationOrderItem",
     *      tags={"PersonalizationOrderItem"},
     *      description="Get PersonalizationOrderItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PersonalizationOrderItem",
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
     *                  ref="#/definitions/PersonalizationOrderItem"
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
        /** @var PersonalizationOrderItem $personalizationOrderItem */
        $personalizationOrderItem = $this->personalizationOrderItemRepository->find($id);

        if (empty($personalizationOrderItem)) {
            return $this->sendError('Personalization Order Item not found');
        }

        return $this->sendResponse($personalizationOrderItem->toArray(), 'Personalization Order Item retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePersonalizationOrderItemAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/personalizationOrderItems/{id}",
     *      summary="Update the specified PersonalizationOrderItem in storage",
     *      tags={"PersonalizationOrderItem"},
     *      description="Update PersonalizationOrderItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PersonalizationOrderItem",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PersonalizationOrderItem that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PersonalizationOrderItem")
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
     *                  ref="#/definitions/PersonalizationOrderItem"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePersonalizationOrderItemAPIRequest $request)
    {
        $input = $request->all();

        /** @var PersonalizationOrderItem $personalizationOrderItem */
        $personalizationOrderItem = $this->personalizationOrderItemRepository->find($id);

        if (empty($personalizationOrderItem)) {
            return $this->sendError('Personalization Order Item not found');
        }

        $personalizationOrderItem = $this->personalizationOrderItemRepository->update($input, $id);

        return $this->sendResponse($personalizationOrderItem->toArray(), 'PersonalizationOrderItem updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/personalizationOrderItems/{id}",
     *      summary="Remove the specified PersonalizationOrderItem from storage",
     *      tags={"PersonalizationOrderItem"},
     *      description="Delete PersonalizationOrderItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PersonalizationOrderItem",
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
        /** @var PersonalizationOrderItem $personalizationOrderItem */
        $personalizationOrderItem = $this->personalizationOrderItemRepository->find($id);

        if (empty($personalizationOrderItem)) {
            return $this->sendError('Personalization Order Item not found');
        }

        $personalizationOrderItem->delete();

        return $this->sendResponse($id, 'Personalization Order Item deleted successfully');
    }
}
