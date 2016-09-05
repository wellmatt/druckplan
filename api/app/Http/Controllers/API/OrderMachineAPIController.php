<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateOrderMachineAPIRequest;
use App\Http\Requests\API\UpdateOrderMachineAPIRequest;
use App\Models\OrderMachine;
use App\Repositories\OrderMachineRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class OrderMachineController
 * @package App\Http\Controllers\API
 */

class OrderMachineAPIController extends AppBaseController
{
    /** @var  OrderMachineRepository */
    private $orderMachineRepository;

    public function __construct(OrderMachineRepository $orderMachineRepo)
    {
        $this->orderMachineRepository = $orderMachineRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/orderMachines",
     *      summary="Get a listing of the OrderMachines.",
     *      tags={"OrderMachine"},
     *      description="Get all OrderMachines",
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
     *                  @SWG\Items(ref="#/definitions/OrderMachine")
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
        $this->orderMachineRepository->pushCriteria(new RequestCriteria($request));
        $this->orderMachineRepository->pushCriteria(new LimitOffsetCriteria($request));
        $orderMachines = $this->orderMachineRepository->all();

        return $this->sendResponse($orderMachines->toArray(), 'Order Machines retrieved successfully');
    }

    /**
     * @param CreateOrderMachineAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/orderMachines",
     *      summary="Store a newly created OrderMachine in storage",
     *      tags={"OrderMachine"},
     *      description="Store OrderMachine",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="OrderMachine that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/OrderMachine")
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
     *                  ref="#/definitions/OrderMachine"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateOrderMachineAPIRequest $request)
    {
        $input = $request->all();

        $orderMachines = $this->orderMachineRepository->create($input);

        return $this->sendResponse($orderMachines->toArray(), 'Order Machine saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/orderMachines/{id}",
     *      summary="Display the specified OrderMachine",
     *      tags={"OrderMachine"},
     *      description="Get OrderMachine",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of OrderMachine",
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
     *                  ref="#/definitions/OrderMachine"
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
        /** @var OrderMachine $orderMachine */
        $orderMachine = $this->orderMachineRepository->find($id);

        if (empty($orderMachine)) {
            return $this->sendError('Order Machine not found');
        }

        return $this->sendResponse($orderMachine->toArray(), 'Order Machine retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateOrderMachineAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/orderMachines/{id}",
     *      summary="Update the specified OrderMachine in storage",
     *      tags={"OrderMachine"},
     *      description="Update OrderMachine",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of OrderMachine",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="OrderMachine that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/OrderMachine")
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
     *                  ref="#/definitions/OrderMachine"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateOrderMachineAPIRequest $request)
    {
        $input = $request->all();

        /** @var OrderMachine $orderMachine */
        $orderMachine = $this->orderMachineRepository->find($id);

        if (empty($orderMachine)) {
            return $this->sendError('Order Machine not found');
        }

        $orderMachine = $this->orderMachineRepository->update($input, $id);

        return $this->sendResponse($orderMachine->toArray(), 'OrderMachine updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/orderMachines/{id}",
     *      summary="Remove the specified OrderMachine from storage",
     *      tags={"OrderMachine"},
     *      description="Delete OrderMachine",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of OrderMachine",
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
        /** @var OrderMachine $orderMachine */
        $orderMachine = $this->orderMachineRepository->find($id);

        if (empty($orderMachine)) {
            return $this->sendError('Order Machine not found');
        }

        $orderMachine->delete();

        return $this->sendResponse($id, 'Order Machine deleted successfully');
    }
}
