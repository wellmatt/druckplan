<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDeliverytermAPIRequest;
use App\Http\Requests\API\UpdateDeliverytermAPIRequest;
use App\Models\Deliveryterm;
use App\Repositories\DeliverytermRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DeliverytermController
 * @package App\Http\Controllers\API
 */

class DeliverytermAPIController extends AppBaseController
{
    /** @var  DeliverytermRepository */
    private $deliverytermRepository;

    public function __construct(DeliverytermRepository $deliverytermRepo)
    {
        $this->deliverytermRepository = $deliverytermRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/deliveryterms",
     *      summary="Get a listing of the Deliveryterms.",
     *      tags={"Deliveryterm"},
     *      description="Get all Deliveryterms",
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
     *                  @SWG\Items(ref="#/definitions/Deliveryterm")
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
        $this->deliverytermRepository->pushCriteria(new RequestCriteria($request));
        $this->deliverytermRepository->pushCriteria(new LimitOffsetCriteria($request));
        $deliveryterms = $this->deliverytermRepository->all();

        return $this->sendResponse($deliveryterms->toArray(), 'Deliveryterms retrieved successfully');
    }

    /**
     * @param CreateDeliverytermAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/deliveryterms",
     *      summary="Store a newly created Deliveryterm in storage",
     *      tags={"Deliveryterm"},
     *      description="Store Deliveryterm",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Deliveryterm that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Deliveryterm")
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
     *                  ref="#/definitions/Deliveryterm"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDeliverytermAPIRequest $request)
    {
        $input = $request->all();

        $deliveryterms = $this->deliverytermRepository->create($input);

        return $this->sendResponse($deliveryterms->toArray(), 'Deliveryterm saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/deliveryterms/{id}",
     *      summary="Display the specified Deliveryterm",
     *      tags={"Deliveryterm"},
     *      description="Get Deliveryterm",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Deliveryterm",
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
     *                  ref="#/definitions/Deliveryterm"
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
        /** @var Deliveryterm $deliveryterm */
        $deliveryterm = $this->deliverytermRepository->find($id);

        if (empty($deliveryterm)) {
            return $this->sendError('Deliveryterm not found');
        }

        return $this->sendResponse($deliveryterm->toArray(), 'Deliveryterm retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDeliverytermAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/deliveryterms/{id}",
     *      summary="Update the specified Deliveryterm in storage",
     *      tags={"Deliveryterm"},
     *      description="Update Deliveryterm",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Deliveryterm",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Deliveryterm that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Deliveryterm")
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
     *                  ref="#/definitions/Deliveryterm"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDeliverytermAPIRequest $request)
    {
        $input = $request->all();

        /** @var Deliveryterm $deliveryterm */
        $deliveryterm = $this->deliverytermRepository->find($id);

        if (empty($deliveryterm)) {
            return $this->sendError('Deliveryterm not found');
        }

        $deliveryterm = $this->deliverytermRepository->update($input, $id);

        return $this->sendResponse($deliveryterm->toArray(), 'Deliveryterm updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/deliveryterms/{id}",
     *      summary="Remove the specified Deliveryterm from storage",
     *      tags={"Deliveryterm"},
     *      description="Delete Deliveryterm",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Deliveryterm",
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
        /** @var Deliveryterm $deliveryterm */
        $deliveryterm = $this->deliverytermRepository->find($id);

        if (empty($deliveryterm)) {
            return $this->sendError('Deliveryterm not found');
        }

        $deliveryterm->delete();

        return $this->sendResponse($id, 'Deliveryterm deleted successfully');
    }
}
