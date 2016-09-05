<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFinishingAPIRequest;
use App\Http\Requests\API\UpdateFinishingAPIRequest;
use App\Models\Finishing;
use App\Repositories\FinishingRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FinishingController
 * @package App\Http\Controllers\API
 */

class FinishingAPIController extends AppBaseController
{
    /** @var  FinishingRepository */
    private $finishingRepository;

    public function __construct(FinishingRepository $finishingRepo)
    {
        $this->finishingRepository = $finishingRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/finishings",
     *      summary="Get a listing of the Finishings.",
     *      tags={"Finishing"},
     *      description="Get all Finishings",
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
     *                  @SWG\Items(ref="#/definitions/Finishing")
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
        $this->finishingRepository->pushCriteria(new RequestCriteria($request));
        $this->finishingRepository->pushCriteria(new LimitOffsetCriteria($request));
        $finishings = $this->finishingRepository->all();

        return $this->sendResponse($finishings->toArray(), 'Finishings retrieved successfully');
    }

    /**
     * @param CreateFinishingAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/finishings",
     *      summary="Store a newly created Finishing in storage",
     *      tags={"Finishing"},
     *      description="Store Finishing",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Finishing that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Finishing")
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
     *                  ref="#/definitions/Finishing"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFinishingAPIRequest $request)
    {
        $input = $request->all();

        $finishings = $this->finishingRepository->create($input);

        return $this->sendResponse($finishings->toArray(), 'Finishing saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/finishings/{id}",
     *      summary="Display the specified Finishing",
     *      tags={"Finishing"},
     *      description="Get Finishing",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Finishing",
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
     *                  ref="#/definitions/Finishing"
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
        /** @var Finishing $finishing */
        $finishing = $this->finishingRepository->find($id);

        if (empty($finishing)) {
            return $this->sendError('Finishing not found');
        }

        return $this->sendResponse($finishing->toArray(), 'Finishing retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateFinishingAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/finishings/{id}",
     *      summary="Update the specified Finishing in storage",
     *      tags={"Finishing"},
     *      description="Update Finishing",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Finishing",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Finishing that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Finishing")
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
     *                  ref="#/definitions/Finishing"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFinishingAPIRequest $request)
    {
        $input = $request->all();

        /** @var Finishing $finishing */
        $finishing = $this->finishingRepository->find($id);

        if (empty($finishing)) {
            return $this->sendError('Finishing not found');
        }

        $finishing = $this->finishingRepository->update($input, $id);

        return $this->sendResponse($finishing->toArray(), 'Finishing updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/finishings/{id}",
     *      summary="Remove the specified Finishing from storage",
     *      tags={"Finishing"},
     *      description="Delete Finishing",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Finishing",
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
        /** @var Finishing $finishing */
        $finishing = $this->finishingRepository->find($id);

        if (empty($finishing)) {
            return $this->sendError('Finishing not found');
        }

        $finishing->delete();

        return $this->sendResponse($id, 'Finishing deleted successfully');
    }
}
