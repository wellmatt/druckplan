<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBusinesscontactAttributeAPIRequest;
use App\Http\Requests\API\UpdateBusinesscontactAttributeAPIRequest;
use App\Models\BusinesscontactAttribute;
use App\Repositories\BusinesscontactAttributeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BusinesscontactAttributeController
 * @package App\Http\Controllers\API
 */

class BusinesscontactAttributeAPIController extends AppBaseController
{
    /** @var  BusinesscontactAttributeRepository */
    private $businesscontactAttributeRepository;

    public function __construct(BusinesscontactAttributeRepository $businesscontactAttributeRepo)
    {
        $this->businesscontactAttributeRepository = $businesscontactAttributeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/businesscontactAttributes",
     *      summary="Get a listing of the BusinesscontactAttributes.",
     *      tags={"BusinesscontactAttribute"},
     *      description="Get all BusinesscontactAttributes",
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
     *                  @SWG\Items(ref="#/definitions/BusinesscontactAttribute")
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
        $this->businesscontactAttributeRepository->pushCriteria(new RequestCriteria($request));
        $this->businesscontactAttributeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $businesscontactAttributes = $this->businesscontactAttributeRepository->all();

        return $this->sendResponse($businesscontactAttributes->toArray(), 'Businesscontact Attributes retrieved successfully');
    }

    /**
     * @param CreateBusinesscontactAttributeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/businesscontactAttributes",
     *      summary="Store a newly created BusinesscontactAttribute in storage",
     *      tags={"BusinesscontactAttribute"},
     *      description="Store BusinesscontactAttribute",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BusinesscontactAttribute that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BusinesscontactAttribute")
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
     *                  ref="#/definitions/BusinesscontactAttribute"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBusinesscontactAttributeAPIRequest $request)
    {
        $input = $request->all();

        $businesscontactAttributes = $this->businesscontactAttributeRepository->create($input);

        return $this->sendResponse($businesscontactAttributes->toArray(), 'Businesscontact Attribute saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/businesscontactAttributes/{id}",
     *      summary="Display the specified BusinesscontactAttribute",
     *      tags={"BusinesscontactAttribute"},
     *      description="Get BusinesscontactAttribute",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BusinesscontactAttribute",
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
     *                  ref="#/definitions/BusinesscontactAttribute"
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
        /** @var BusinesscontactAttribute $businesscontactAttribute */
        $businesscontactAttribute = $this->businesscontactAttributeRepository->find($id);

        if (empty($businesscontactAttribute)) {
            return $this->sendError('Businesscontact Attribute not found');
        }

        return $this->sendResponse($businesscontactAttribute->toArray(), 'Businesscontact Attribute retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateBusinesscontactAttributeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/businesscontactAttributes/{id}",
     *      summary="Update the specified BusinesscontactAttribute in storage",
     *      tags={"BusinesscontactAttribute"},
     *      description="Update BusinesscontactAttribute",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BusinesscontactAttribute",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BusinesscontactAttribute that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BusinesscontactAttribute")
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
     *                  ref="#/definitions/BusinesscontactAttribute"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBusinesscontactAttributeAPIRequest $request)
    {
        $input = $request->all();

        /** @var BusinesscontactAttribute $businesscontactAttribute */
        $businesscontactAttribute = $this->businesscontactAttributeRepository->find($id);

        if (empty($businesscontactAttribute)) {
            return $this->sendError('Businesscontact Attribute not found');
        }

        $businesscontactAttribute = $this->businesscontactAttributeRepository->update($input, $id);

        return $this->sendResponse($businesscontactAttribute->toArray(), 'BusinesscontactAttribute updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/businesscontactAttributes/{id}",
     *      summary="Remove the specified BusinesscontactAttribute from storage",
     *      tags={"BusinesscontactAttribute"},
     *      description="Delete BusinesscontactAttribute",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BusinesscontactAttribute",
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
        /** @var BusinesscontactAttribute $businesscontactAttribute */
        $businesscontactAttribute = $this->businesscontactAttributeRepository->find($id);

        if (empty($businesscontactAttribute)) {
            return $this->sendError('Businesscontact Attribute not found');
        }

        $businesscontactAttribute->delete();

        return $this->sendResponse($id, 'Businesscontact Attribute deleted successfully');
    }
}
