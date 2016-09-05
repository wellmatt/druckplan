<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupOrderPositionAPIRequest;
use App\Http\Requests\API\UpdateSupOrderPositionAPIRequest;
use App\Models\SupOrderPosition;
use App\Repositories\SupOrderPositionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupOrderPositionController
 * @package App\Http\Controllers\API
 */

class SupOrderPositionAPIController extends AppBaseController
{
    /** @var  SupOrderPositionRepository */
    private $supOrderPositionRepository;

    public function __construct(SupOrderPositionRepository $supOrderPositionRepo)
    {
        $this->supOrderPositionRepository = $supOrderPositionRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/supOrderPositions",
     *      summary="Get a listing of the SupOrderPositions.",
     *      tags={"SupOrderPosition"},
     *      description="Get all SupOrderPositions",
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
     *                  @SWG\Items(ref="#/definitions/SupOrderPosition")
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
        $this->supOrderPositionRepository->pushCriteria(new RequestCriteria($request));
        $this->supOrderPositionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supOrderPositions = $this->supOrderPositionRepository->all();

        return $this->sendResponse($supOrderPositions->toArray(), 'Sup Order Positions retrieved successfully');
    }

    /**
     * @param CreateSupOrderPositionAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/supOrderPositions",
     *      summary="Store a newly created SupOrderPosition in storage",
     *      tags={"SupOrderPosition"},
     *      description="Store SupOrderPosition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupOrderPosition that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupOrderPosition")
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
     *                  ref="#/definitions/SupOrderPosition"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupOrderPositionAPIRequest $request)
    {
        $input = $request->all();

        $supOrderPositions = $this->supOrderPositionRepository->create($input);

        return $this->sendResponse($supOrderPositions->toArray(), 'Sup Order Position saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/supOrderPositions/{id}",
     *      summary="Display the specified SupOrderPosition",
     *      tags={"SupOrderPosition"},
     *      description="Get SupOrderPosition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupOrderPosition",
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
     *                  ref="#/definitions/SupOrderPosition"
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
        /** @var SupOrderPosition $supOrderPosition */
        $supOrderPosition = $this->supOrderPositionRepository->find($id);

        if (empty($supOrderPosition)) {
            return $this->sendError('Sup Order Position not found');
        }

        return $this->sendResponse($supOrderPosition->toArray(), 'Sup Order Position retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateSupOrderPositionAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/supOrderPositions/{id}",
     *      summary="Update the specified SupOrderPosition in storage",
     *      tags={"SupOrderPosition"},
     *      description="Update SupOrderPosition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupOrderPosition",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupOrderPosition that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupOrderPosition")
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
     *                  ref="#/definitions/SupOrderPosition"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupOrderPositionAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupOrderPosition $supOrderPosition */
        $supOrderPosition = $this->supOrderPositionRepository->find($id);

        if (empty($supOrderPosition)) {
            return $this->sendError('Sup Order Position not found');
        }

        $supOrderPosition = $this->supOrderPositionRepository->update($input, $id);

        return $this->sendResponse($supOrderPosition->toArray(), 'SupOrderPosition updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/supOrderPositions/{id}",
     *      summary="Remove the specified SupOrderPosition from storage",
     *      tags={"SupOrderPosition"},
     *      description="Delete SupOrderPosition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupOrderPosition",
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
        /** @var SupOrderPosition $supOrderPosition */
        $supOrderPosition = $this->supOrderPositionRepository->find($id);

        if (empty($supOrderPosition)) {
            return $this->sendError('Sup Order Position not found');
        }

        $supOrderPosition->delete();

        return $this->sendResponse($id, 'Sup Order Position deleted successfully');
    }
}
