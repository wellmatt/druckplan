<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePersonalizationItemAPIRequest;
use App\Http\Requests\API\UpdatePersonalizationItemAPIRequest;
use App\Models\PersonalizationItem;
use App\Repositories\PersonalizationItemRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PersonalizationItemController
 * @package App\Http\Controllers\API
 */

class PersonalizationItemAPIController extends AppBaseController
{
    /** @var  PersonalizationItemRepository */
    private $personalizationItemRepository;

    public function __construct(PersonalizationItemRepository $personalizationItemRepo)
    {
        $this->personalizationItemRepository = $personalizationItemRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/personalizationItems",
     *      summary="Get a listing of the PersonalizationItems.",
     *      tags={"PersonalizationItem"},
     *      description="Get all PersonalizationItems",
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
     *                  @SWG\Items(ref="#/definitions/PersonalizationItem")
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
        $this->personalizationItemRepository->pushCriteria(new RequestCriteria($request));
        $this->personalizationItemRepository->pushCriteria(new LimitOffsetCriteria($request));
        $personalizationItems = $this->personalizationItemRepository->all();

        return $this->sendResponse($personalizationItems->toArray(), 'Personalization Items retrieved successfully');
    }

    /**
     * @param CreatePersonalizationItemAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/personalizationItems",
     *      summary="Store a newly created PersonalizationItem in storage",
     *      tags={"PersonalizationItem"},
     *      description="Store PersonalizationItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PersonalizationItem that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PersonalizationItem")
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
     *                  ref="#/definitions/PersonalizationItem"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePersonalizationItemAPIRequest $request)
    {
        $input = $request->all();

        $personalizationItems = $this->personalizationItemRepository->create($input);

        return $this->sendResponse($personalizationItems->toArray(), 'Personalization Item saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/personalizationItems/{id}",
     *      summary="Display the specified PersonalizationItem",
     *      tags={"PersonalizationItem"},
     *      description="Get PersonalizationItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PersonalizationItem",
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
     *                  ref="#/definitions/PersonalizationItem"
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
        /** @var PersonalizationItem $personalizationItem */
        $personalizationItem = $this->personalizationItemRepository->find($id);

        if (empty($personalizationItem)) {
            return $this->sendError('Personalization Item not found');
        }

        return $this->sendResponse($personalizationItem->toArray(), 'Personalization Item retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePersonalizationItemAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/personalizationItems/{id}",
     *      summary="Update the specified PersonalizationItem in storage",
     *      tags={"PersonalizationItem"},
     *      description="Update PersonalizationItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PersonalizationItem",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PersonalizationItem that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PersonalizationItem")
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
     *                  ref="#/definitions/PersonalizationItem"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePersonalizationItemAPIRequest $request)
    {
        $input = $request->all();

        /** @var PersonalizationItem $personalizationItem */
        $personalizationItem = $this->personalizationItemRepository->find($id);

        if (empty($personalizationItem)) {
            return $this->sendError('Personalization Item not found');
        }

        $personalizationItem = $this->personalizationItemRepository->update($input, $id);

        return $this->sendResponse($personalizationItem->toArray(), 'PersonalizationItem updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/personalizationItems/{id}",
     *      summary="Remove the specified PersonalizationItem from storage",
     *      tags={"PersonalizationItem"},
     *      description="Delete PersonalizationItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PersonalizationItem",
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
        /** @var PersonalizationItem $personalizationItem */
        $personalizationItem = $this->personalizationItemRepository->find($id);

        if (empty($personalizationItem)) {
            return $this->sendError('Personalization Item not found');
        }

        $personalizationItem->delete();

        return $this->sendResponse($id, 'Personalization Item deleted successfully');
    }
}
