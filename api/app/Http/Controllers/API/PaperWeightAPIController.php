<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaperWeightAPIRequest;
use App\Http\Requests\API\UpdatePaperWeightAPIRequest;
use App\Models\PaperWeight;
use App\Repositories\PaperWeightRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaperWeightController
 * @package App\Http\Controllers\API
 */

class PaperWeightAPIController extends AppBaseController
{
    /** @var  PaperWeightRepository */
    private $paperWeightRepository;

    public function __construct(PaperWeightRepository $paperWeightRepo)
    {
        $this->paperWeightRepository = $paperWeightRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paperWeights",
     *      summary="Get a listing of the PaperWeights.",
     *      tags={"PaperWeight"},
     *      description="Get all PaperWeights",
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
     *                  @SWG\Items(ref="#/definitions/PaperWeight")
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
        $this->paperWeightRepository->pushCriteria(new RequestCriteria($request));
        $this->paperWeightRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paperWeights = $this->paperWeightRepository->all();

        return $this->sendResponse($paperWeights->toArray(), 'Paper Weights retrieved successfully');
    }

    /**
     * @param CreatePaperWeightAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paperWeights",
     *      summary="Store a newly created PaperWeight in storage",
     *      tags={"PaperWeight"},
     *      description="Store PaperWeight",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaperWeight that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaperWeight")
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
     *                  ref="#/definitions/PaperWeight"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaperWeightAPIRequest $request)
    {
        $input = $request->all();

        $paperWeights = $this->paperWeightRepository->create($input);

        return $this->sendResponse($paperWeights->toArray(), 'Paper Weight saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/paperWeights/{id}",
     *      summary="Display the specified PaperWeight",
     *      tags={"PaperWeight"},
     *      description="Get PaperWeight",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaperWeight",
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
     *                  ref="#/definitions/PaperWeight"
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
        /** @var PaperWeight $paperWeight */
        $paperWeight = $this->paperWeightRepository->find($id);

        if (empty($paperWeight)) {
            return $this->sendError('Paper Weight not found');
        }

        return $this->sendResponse($paperWeight->toArray(), 'Paper Weight retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePaperWeightAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paperWeights/{id}",
     *      summary="Update the specified PaperWeight in storage",
     *      tags={"PaperWeight"},
     *      description="Update PaperWeight",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaperWeight",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaperWeight that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaperWeight")
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
     *                  ref="#/definitions/PaperWeight"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaperWeightAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaperWeight $paperWeight */
        $paperWeight = $this->paperWeightRepository->find($id);

        if (empty($paperWeight)) {
            return $this->sendError('Paper Weight not found');
        }

        $paperWeight = $this->paperWeightRepository->update($input, $id);

        return $this->sendResponse($paperWeight->toArray(), 'PaperWeight updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paperWeights/{id}",
     *      summary="Remove the specified PaperWeight from storage",
     *      tags={"PaperWeight"},
     *      description="Delete PaperWeight",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaperWeight",
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
        /** @var PaperWeight $paperWeight */
        $paperWeight = $this->paperWeightRepository->find($id);

        if (empty($paperWeight)) {
            return $this->sendError('Paper Weight not found');
        }

        $paperWeight->delete();

        return $this->sendResponse($id, 'Paper Weight deleted successfully');
    }
}
