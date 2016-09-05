<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePersonalizationOrderAPIRequest;
use App\Http\Requests\API\UpdatePersonalizationOrderAPIRequest;
use App\Models\PersonalizationOrder;
use App\Repositories\PersonalizationOrderRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PersonalizationOrderController
 * @package App\Http\Controllers\API
 */

class PersonalizationOrderAPIController extends AppBaseController
{
    /** @var  PersonalizationOrderRepository */
    private $personalizationOrderRepository;

    public function __construct(PersonalizationOrderRepository $personalizationOrderRepo)
    {
        $this->personalizationOrderRepository = $personalizationOrderRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/personalizationOrders",
     *      summary="Get a listing of the PersonalizationOrders.",
     *      tags={"PersonalizationOrder"},
     *      description="Get all PersonalizationOrders",
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
     *                  @SWG\Items(ref="#/definitions/PersonalizationOrder")
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
        $this->personalizationOrderRepository->pushCriteria(new RequestCriteria($request));
        $this->personalizationOrderRepository->pushCriteria(new LimitOffsetCriteria($request));
        $personalizationOrders = $this->personalizationOrderRepository->all();

        return $this->sendResponse($personalizationOrders->toArray(), 'Personalization Orders retrieved successfully');
    }

    /**
     * @param CreatePersonalizationOrderAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/personalizationOrders",
     *      summary="Store a newly created PersonalizationOrder in storage",
     *      tags={"PersonalizationOrder"},
     *      description="Store PersonalizationOrder",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PersonalizationOrder that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PersonalizationOrder")
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
     *                  ref="#/definitions/PersonalizationOrder"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePersonalizationOrderAPIRequest $request)
    {
        $input = $request->all();

        $personalizationOrders = $this->personalizationOrderRepository->create($input);

        return $this->sendResponse($personalizationOrders->toArray(), 'Personalization Order saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/personalizationOrders/{id}",
     *      summary="Display the specified PersonalizationOrder",
     *      tags={"PersonalizationOrder"},
     *      description="Get PersonalizationOrder",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PersonalizationOrder",
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
     *                  ref="#/definitions/PersonalizationOrder"
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
        /** @var PersonalizationOrder $personalizationOrder */
        $personalizationOrder = $this->personalizationOrderRepository->find($id);

        if (empty($personalizationOrder)) {
            return $this->sendError('Personalization Order not found');
        }

        return $this->sendResponse($personalizationOrder->toArray(), 'Personalization Order retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePersonalizationOrderAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/personalizationOrders/{id}",
     *      summary="Update the specified PersonalizationOrder in storage",
     *      tags={"PersonalizationOrder"},
     *      description="Update PersonalizationOrder",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PersonalizationOrder",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PersonalizationOrder that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PersonalizationOrder")
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
     *                  ref="#/definitions/PersonalizationOrder"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePersonalizationOrderAPIRequest $request)
    {
        $input = $request->all();

        /** @var PersonalizationOrder $personalizationOrder */
        $personalizationOrder = $this->personalizationOrderRepository->find($id);

        if (empty($personalizationOrder)) {
            return $this->sendError('Personalization Order not found');
        }

        $personalizationOrder = $this->personalizationOrderRepository->update($input, $id);

        return $this->sendResponse($personalizationOrder->toArray(), 'PersonalizationOrder updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/personalizationOrders/{id}",
     *      summary="Remove the specified PersonalizationOrder from storage",
     *      tags={"PersonalizationOrder"},
     *      description="Delete PersonalizationOrder",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PersonalizationOrder",
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
        /** @var PersonalizationOrder $personalizationOrder */
        $personalizationOrder = $this->personalizationOrderRepository->find($id);

        if (empty($personalizationOrder)) {
            return $this->sendError('Personalization Order not found');
        }

        $personalizationOrder->delete();

        return $this->sendResponse($id, 'Personalization Order deleted successfully');
    }
}
