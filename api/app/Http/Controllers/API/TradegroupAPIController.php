<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTradegroupAPIRequest;
use App\Http\Requests\API\UpdateTradegroupAPIRequest;
use App\Models\Tradegroup;
use App\Repositories\TradegroupRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TradegroupController
 * @package App\Http\Controllers\API
 */

class TradegroupAPIController extends AppBaseController
{
    /** @var  TradegroupRepository */
    private $tradegroupRepository;

    public function __construct(TradegroupRepository $tradegroupRepo)
    {
        $this->tradegroupRepository = $tradegroupRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tradegroups",
     *      summary="Get a listing of the Tradegroups.",
     *      tags={"Tradegroup"},
     *      description="Get all Tradegroups",
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
     *                  @SWG\Items(ref="#/definitions/Tradegroup")
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
        $this->tradegroupRepository->pushCriteria(new RequestCriteria($request));
        $this->tradegroupRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tradegroups = $this->tradegroupRepository->all();

        return $this->sendResponse($tradegroups->toArray(), 'Tradegroups retrieved successfully');
    }

    /**
     * @param CreateTradegroupAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tradegroups",
     *      summary="Store a newly created Tradegroup in storage",
     *      tags={"Tradegroup"},
     *      description="Store Tradegroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Tradegroup that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Tradegroup")
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
     *                  ref="#/definitions/Tradegroup"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTradegroupAPIRequest $request)
    {
        $input = $request->all();

        $tradegroups = $this->tradegroupRepository->create($input);

        return $this->sendResponse($tradegroups->toArray(), 'Tradegroup saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tradegroups/{id}",
     *      summary="Display the specified Tradegroup",
     *      tags={"Tradegroup"},
     *      description="Get Tradegroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Tradegroup",
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
     *                  ref="#/definitions/Tradegroup"
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
        /** @var Tradegroup $tradegroup */
        $tradegroup = $this->tradegroupRepository->find($id);

        if (empty($tradegroup)) {
            return $this->sendError('Tradegroup not found');
        }

        return $this->sendResponse($tradegroup->toArray(), 'Tradegroup retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTradegroupAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tradegroups/{id}",
     *      summary="Update the specified Tradegroup in storage",
     *      tags={"Tradegroup"},
     *      description="Update Tradegroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Tradegroup",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Tradegroup that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Tradegroup")
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
     *                  ref="#/definitions/Tradegroup"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTradegroupAPIRequest $request)
    {
        $input = $request->all();

        /** @var Tradegroup $tradegroup */
        $tradegroup = $this->tradegroupRepository->find($id);

        if (empty($tradegroup)) {
            return $this->sendError('Tradegroup not found');
        }

        $tradegroup = $this->tradegroupRepository->update($input, $id);

        return $this->sendResponse($tradegroup->toArray(), 'Tradegroup updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tradegroups/{id}",
     *      summary="Remove the specified Tradegroup from storage",
     *      tags={"Tradegroup"},
     *      description="Delete Tradegroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Tradegroup",
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
        /** @var Tradegroup $tradegroup */
        $tradegroup = $this->tradegroupRepository->find($id);

        if (empty($tradegroup)) {
            return $this->sendError('Tradegroup not found');
        }

        $tradegroup->delete();

        return $this->sendResponse($id, 'Tradegroup deleted successfully');
    }
}
