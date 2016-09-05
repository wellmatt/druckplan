<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateOrderCalculationAPIRequest;
use App\Http\Requests\API\UpdateOrderCalculationAPIRequest;
use App\Models\OrderCalculation;
use App\Repositories\OrderCalculationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class OrderCalculationController
 * @package App\Http\Controllers\API
 */

class OrderCalculationAPIController extends AppBaseController
{
    /** @var  OrderCalculationRepository */
    private $orderCalculationRepository;

    public function __construct(OrderCalculationRepository $orderCalculationRepo)
    {
        $this->orderCalculationRepository = $orderCalculationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/orderCalculations",
     *      summary="Get a listing of the OrderCalculations.",
     *      tags={"OrderCalculation"},
     *      description="Get all OrderCalculations",
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
     *                  @SWG\Items(ref="#/definitions/OrderCalculation")
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
        $this->orderCalculationRepository->pushCriteria(new RequestCriteria($request));
        $this->orderCalculationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $orderCalculations = $this->orderCalculationRepository->all();

        return $this->sendResponse($orderCalculations->toArray(), 'Order Calculations retrieved successfully');
    }

    /**
     * @param CreateOrderCalculationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/orderCalculations",
     *      summary="Store a newly created OrderCalculation in storage",
     *      tags={"OrderCalculation"},
     *      description="Store OrderCalculation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="OrderCalculation that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/OrderCalculation")
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
     *                  ref="#/definitions/OrderCalculation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateOrderCalculationAPIRequest $request)
    {
        $input = $request->all();

        $orderCalculations = $this->orderCalculationRepository->create($input);

        return $this->sendResponse($orderCalculations->toArray(), 'Order Calculation saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/orderCalculations/{id}",
     *      summary="Display the specified OrderCalculation",
     *      tags={"OrderCalculation"},
     *      description="Get OrderCalculation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of OrderCalculation",
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
     *                  ref="#/definitions/OrderCalculation"
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
        /** @var OrderCalculation $orderCalculation */
        $orderCalculation = $this->orderCalculationRepository->find($id);

        if (empty($orderCalculation)) {
            return $this->sendError('Order Calculation not found');
        }

        return $this->sendResponse($orderCalculation->toArray(), 'Order Calculation retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateOrderCalculationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/orderCalculations/{id}",
     *      summary="Update the specified OrderCalculation in storage",
     *      tags={"OrderCalculation"},
     *      description="Update OrderCalculation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of OrderCalculation",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="OrderCalculation that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/OrderCalculation")
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
     *                  ref="#/definitions/OrderCalculation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateOrderCalculationAPIRequest $request)
    {
        $input = $request->all();

        /** @var OrderCalculation $orderCalculation */
        $orderCalculation = $this->orderCalculationRepository->find($id);

        if (empty($orderCalculation)) {
            return $this->sendError('Order Calculation not found');
        }

        $orderCalculation = $this->orderCalculationRepository->update($input, $id);

        return $this->sendResponse($orderCalculation->toArray(), 'OrderCalculation updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/orderCalculations/{id}",
     *      summary="Remove the specified OrderCalculation from storage",
     *      tags={"OrderCalculation"},
     *      description="Delete OrderCalculation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of OrderCalculation",
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
        /** @var OrderCalculation $orderCalculation */
        $orderCalculation = $this->orderCalculationRepository->find($id);

        if (empty($orderCalculation)) {
            return $this->sendError('Order Calculation not found');
        }

        $orderCalculation->delete();

        return $this->sendResponse($id, 'Order Calculation deleted successfully');
    }
}
