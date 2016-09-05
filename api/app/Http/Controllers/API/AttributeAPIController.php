<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAttributeAPIRequest;
use App\Http\Requests\API\UpdateAttributeAPIRequest;
use App\Models\Attribute;
use App\Repositories\AttributeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AttributeController
 * @package App\Http\Controllers\API
 */

class AttributeAPIController extends AppBaseController
{
    /** @var  AttributeRepository */
    private $attributeRepository;

    public function __construct(AttributeRepository $attributeRepo)
    {
        $this->attributeRepository = $attributeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/attributes",
     *      summary="Get a listing of the Attributes.",
     *      tags={"Attribute"},
     *      description="Get all Attributes",
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
     *                  @SWG\Items(ref="#/definitions/Attribute")
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
        $this->attributeRepository->pushCriteria(new RequestCriteria($request));
        $this->attributeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $attributes = $this->attributeRepository->all();

        return $this->sendResponse($attributes->toArray(), 'Attributes retrieved successfully');
    }

    /**
     * @param CreateAttributeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/attributes",
     *      summary="Store a newly created Attribute in storage",
     *      tags={"Attribute"},
     *      description="Store Attribute",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Attribute that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Attribute")
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
     *                  ref="#/definitions/Attribute"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAttributeAPIRequest $request)
    {
        $input = $request->all();

        $attributes = $this->attributeRepository->create($input);

        return $this->sendResponse($attributes->toArray(), 'Attribute saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/attributes/{id}",
     *      summary="Display the specified Attribute",
     *      tags={"Attribute"},
     *      description="Get Attribute",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Attribute",
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
     *                  ref="#/definitions/Attribute"
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
        /** @var Attribute $attribute */
        $attribute = $this->attributeRepository->find($id);

        if (empty($attribute)) {
            return $this->sendError('Attribute not found');
        }

        return $this->sendResponse($attribute->toArray(), 'Attribute retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAttributeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/attributes/{id}",
     *      summary="Update the specified Attribute in storage",
     *      tags={"Attribute"},
     *      description="Update Attribute",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Attribute",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Attribute that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Attribute")
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
     *                  ref="#/definitions/Attribute"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAttributeAPIRequest $request)
    {
        $input = $request->all();

        /** @var Attribute $attribute */
        $attribute = $this->attributeRepository->find($id);

        if (empty($attribute)) {
            return $this->sendError('Attribute not found');
        }

        $attribute = $this->attributeRepository->update($input, $id);

        return $this->sendResponse($attribute->toArray(), 'Attribute updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/attributes/{id}",
     *      summary="Remove the specified Attribute from storage",
     *      tags={"Attribute"},
     *      description="Delete Attribute",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Attribute",
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
        /** @var Attribute $attribute */
        $attribute = $this->attributeRepository->find($id);

        if (empty($attribute)) {
            return $this->sendError('Attribute not found');
        }

        $attribute->delete();

        return $this->sendResponse($id, 'Attribute deleted successfully');
    }
}
