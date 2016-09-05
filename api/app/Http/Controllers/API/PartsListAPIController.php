<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePartsListAPIRequest;
use App\Http\Requests\API\UpdatePartsListAPIRequest;
use App\Models\PartsList;
use App\Repositories\PartsListRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PartsListController
 * @package App\Http\Controllers\API
 */

class PartsListAPIController extends AppBaseController
{
    /** @var  PartsListRepository */
    private $partsListRepository;

    public function __construct(PartsListRepository $partsListRepo)
    {
        $this->partsListRepository = $partsListRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/partsLists",
     *      summary="Get a listing of the PartsLists.",
     *      tags={"PartsList"},
     *      description="Get all PartsLists",
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
     *                  @SWG\Items(ref="#/definitions/PartsList")
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
        $this->partsListRepository->pushCriteria(new RequestCriteria($request));
        $this->partsListRepository->pushCriteria(new LimitOffsetCriteria($request));
        $partsLists = $this->partsListRepository->all();

        return $this->sendResponse($partsLists->toArray(), 'Parts Lists retrieved successfully');
    }

    /**
     * @param CreatePartsListAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/partsLists",
     *      summary="Store a newly created PartsList in storage",
     *      tags={"PartsList"},
     *      description="Store PartsList",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PartsList that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PartsList")
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
     *                  ref="#/definitions/PartsList"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePartsListAPIRequest $request)
    {
        $input = $request->all();

        $partsLists = $this->partsListRepository->create($input);

        return $this->sendResponse($partsLists->toArray(), 'Parts List saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/partsLists/{id}",
     *      summary="Display the specified PartsList",
     *      tags={"PartsList"},
     *      description="Get PartsList",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PartsList",
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
     *                  ref="#/definitions/PartsList"
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
        /** @var PartsList $partsList */
        $partsList = $this->partsListRepository->find($id);

        if (empty($partsList)) {
            return $this->sendError('Parts List not found');
        }

        return $this->sendResponse($partsList->toArray(), 'Parts List retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePartsListAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/partsLists/{id}",
     *      summary="Update the specified PartsList in storage",
     *      tags={"PartsList"},
     *      description="Update PartsList",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PartsList",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PartsList that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PartsList")
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
     *                  ref="#/definitions/PartsList"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePartsListAPIRequest $request)
    {
        $input = $request->all();

        /** @var PartsList $partsList */
        $partsList = $this->partsListRepository->find($id);

        if (empty($partsList)) {
            return $this->sendError('Parts List not found');
        }

        $partsList = $this->partsListRepository->update($input, $id);

        return $this->sendResponse($partsList->toArray(), 'PartsList updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/partsLists/{id}",
     *      summary="Remove the specified PartsList from storage",
     *      tags={"PartsList"},
     *      description="Delete PartsList",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PartsList",
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
        /** @var PartsList $partsList */
        $partsList = $this->partsListRepository->find($id);

        if (empty($partsList)) {
            return $this->sendError('Parts List not found');
        }

        $partsList->delete();

        return $this->sendResponse($id, 'Parts List deleted successfully');
    }
}
