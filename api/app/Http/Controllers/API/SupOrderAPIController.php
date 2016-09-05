<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupOrderAPIRequest;
use App\Http\Requests\API\UpdateSupOrderAPIRequest;
use App\Models\SupOrder;
use App\Repositories\SupOrderRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupOrderController
 * @package App\Http\Controllers\API
 */

class SupOrderAPIController extends AppBaseController
{
    /** @var  SupOrderRepository */
    private $supOrderRepository;

    public function __construct(SupOrderRepository $supOrderRepo)
    {
        $this->supOrderRepository = $supOrderRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/supOrders",
     *      summary="Get a listing of the SupOrders.",
     *      tags={"SupOrder"},
     *      description="Get all SupOrders",
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
     *                  @SWG\Items(ref="#/definitions/SupOrder")
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
        $this->supOrderRepository->pushCriteria(new RequestCriteria($request));
        $this->supOrderRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supOrders = $this->supOrderRepository->all();

        return $this->sendResponse($supOrders->toArray(), 'Sup Orders retrieved successfully');
    }

    /**
     * @param CreateSupOrderAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/supOrders",
     *      summary="Store a newly created SupOrder in storage",
     *      tags={"SupOrder"},
     *      description="Store SupOrder",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupOrder that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupOrder")
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
     *                  ref="#/definitions/SupOrder"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupOrderAPIRequest $request)
    {
        $input = $request->all();

        $supOrders = $this->supOrderRepository->create($input);

        return $this->sendResponse($supOrders->toArray(), 'Sup Order saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/supOrders/{id}",
     *      summary="Display the specified SupOrder",
     *      tags={"SupOrder"},
     *      description="Get SupOrder",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupOrder",
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
     *                  ref="#/definitions/SupOrder"
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
        /** @var SupOrder $supOrder */
        $supOrder = $this->supOrderRepository->find($id);

        if (empty($supOrder)) {
            return $this->sendError('Sup Order not found');
        }

        return $this->sendResponse($supOrder->toArray(), 'Sup Order retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateSupOrderAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/supOrders/{id}",
     *      summary="Update the specified SupOrder in storage",
     *      tags={"SupOrder"},
     *      description="Update SupOrder",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupOrder",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupOrder that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupOrder")
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
     *                  ref="#/definitions/SupOrder"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupOrderAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupOrder $supOrder */
        $supOrder = $this->supOrderRepository->find($id);

        if (empty($supOrder)) {
            return $this->sendError('Sup Order not found');
        }

        $supOrder = $this->supOrderRepository->update($input, $id);

        return $this->sendResponse($supOrder->toArray(), 'SupOrder updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/supOrders/{id}",
     *      summary="Remove the specified SupOrder from storage",
     *      tags={"SupOrder"},
     *      description="Delete SupOrder",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupOrder",
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
        /** @var SupOrder $supOrder */
        $supOrder = $this->supOrderRepository->find($id);

        if (empty($supOrder)) {
            return $this->sendError('Sup Order not found');
        }

        $supOrder->delete();

        return $this->sendResponse($id, 'Sup Order deleted successfully');
    }
}
