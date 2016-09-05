<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFoldtypeAPIRequest;
use App\Http\Requests\API\UpdateFoldtypeAPIRequest;
use App\Models\Foldtype;
use App\Repositories\FoldtypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FoldtypeController
 * @package App\Http\Controllers\API
 */

class FoldtypeAPIController extends AppBaseController
{
    /** @var  FoldtypeRepository */
    private $foldtypeRepository;

    public function __construct(FoldtypeRepository $foldtypeRepo)
    {
        $this->foldtypeRepository = $foldtypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/foldtypes",
     *      summary="Get a listing of the Foldtypes.",
     *      tags={"Foldtype"},
     *      description="Get all Foldtypes",
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
     *                  @SWG\Items(ref="#/definitions/Foldtype")
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
        $this->foldtypeRepository->pushCriteria(new RequestCriteria($request));
        $this->foldtypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $foldtypes = $this->foldtypeRepository->all();

        return $this->sendResponse($foldtypes->toArray(), 'Foldtypes retrieved successfully');
    }

    /**
     * @param CreateFoldtypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/foldtypes",
     *      summary="Store a newly created Foldtype in storage",
     *      tags={"Foldtype"},
     *      description="Store Foldtype",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Foldtype that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Foldtype")
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
     *                  ref="#/definitions/Foldtype"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFoldtypeAPIRequest $request)
    {
        $input = $request->all();

        $foldtypes = $this->foldtypeRepository->create($input);

        return $this->sendResponse($foldtypes->toArray(), 'Foldtype saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/foldtypes/{id}",
     *      summary="Display the specified Foldtype",
     *      tags={"Foldtype"},
     *      description="Get Foldtype",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Foldtype",
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
     *                  ref="#/definitions/Foldtype"
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
        /** @var Foldtype $foldtype */
        $foldtype = $this->foldtypeRepository->find($id);

        if (empty($foldtype)) {
            return $this->sendError('Foldtype not found');
        }

        return $this->sendResponse($foldtype->toArray(), 'Foldtype retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateFoldtypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/foldtypes/{id}",
     *      summary="Update the specified Foldtype in storage",
     *      tags={"Foldtype"},
     *      description="Update Foldtype",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Foldtype",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Foldtype that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Foldtype")
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
     *                  ref="#/definitions/Foldtype"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFoldtypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var Foldtype $foldtype */
        $foldtype = $this->foldtypeRepository->find($id);

        if (empty($foldtype)) {
            return $this->sendError('Foldtype not found');
        }

        $foldtype = $this->foldtypeRepository->update($input, $id);

        return $this->sendResponse($foldtype->toArray(), 'Foldtype updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/foldtypes/{id}",
     *      summary="Remove the specified Foldtype from storage",
     *      tags={"Foldtype"},
     *      description="Delete Foldtype",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Foldtype",
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
        /** @var Foldtype $foldtype */
        $foldtype = $this->foldtypeRepository->find($id);

        if (empty($foldtype)) {
            return $this->sendError('Foldtype not found');
        }

        $foldtype->delete();

        return $this->sendResponse($id, 'Foldtype deleted successfully');
    }
}
