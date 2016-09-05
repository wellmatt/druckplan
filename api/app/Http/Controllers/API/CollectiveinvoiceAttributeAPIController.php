<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCollectiveinvoiceAttributeAPIRequest;
use App\Http\Requests\API\UpdateCollectiveinvoiceAttributeAPIRequest;
use App\Models\CollectiveinvoiceAttribute;
use App\Repositories\CollectiveinvoiceAttributeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CollectiveinvoiceAttributeController
 * @package App\Http\Controllers\API
 */

class CollectiveinvoiceAttributeAPIController extends AppBaseController
{
    /** @var  CollectiveinvoiceAttributeRepository */
    private $collectiveinvoiceAttributeRepository;

    public function __construct(CollectiveinvoiceAttributeRepository $collectiveinvoiceAttributeRepo)
    {
        $this->collectiveinvoiceAttributeRepository = $collectiveinvoiceAttributeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/collectiveinvoiceAttributes",
     *      summary="Get a listing of the CollectiveinvoiceAttributes.",
     *      tags={"CollectiveinvoiceAttribute"},
     *      description="Get all CollectiveinvoiceAttributes",
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
     *                  @SWG\Items(ref="#/definitions/CollectiveinvoiceAttribute")
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
        $this->collectiveinvoiceAttributeRepository->pushCriteria(new RequestCriteria($request));
        $this->collectiveinvoiceAttributeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $collectiveinvoiceAttributes = $this->collectiveinvoiceAttributeRepository->all();

        return $this->sendResponse($collectiveinvoiceAttributes->toArray(), 'Collectiveinvoice Attributes retrieved successfully');
    }

    /**
     * @param CreateCollectiveinvoiceAttributeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/collectiveinvoiceAttributes",
     *      summary="Store a newly created CollectiveinvoiceAttribute in storage",
     *      tags={"CollectiveinvoiceAttribute"},
     *      description="Store CollectiveinvoiceAttribute",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CollectiveinvoiceAttribute that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CollectiveinvoiceAttribute")
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
     *                  ref="#/definitions/CollectiveinvoiceAttribute"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCollectiveinvoiceAttributeAPIRequest $request)
    {
        $input = $request->all();

        $collectiveinvoiceAttributes = $this->collectiveinvoiceAttributeRepository->create($input);

        return $this->sendResponse($collectiveinvoiceAttributes->toArray(), 'Collectiveinvoice Attribute saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/collectiveinvoiceAttributes/{id}",
     *      summary="Display the specified CollectiveinvoiceAttribute",
     *      tags={"CollectiveinvoiceAttribute"},
     *      description="Get CollectiveinvoiceAttribute",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CollectiveinvoiceAttribute",
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
     *                  ref="#/definitions/CollectiveinvoiceAttribute"
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
        /** @var CollectiveinvoiceAttribute $collectiveinvoiceAttribute */
        $collectiveinvoiceAttribute = $this->collectiveinvoiceAttributeRepository->find($id);

        if (empty($collectiveinvoiceAttribute)) {
            return $this->sendError('Collectiveinvoice Attribute not found');
        }

        return $this->sendResponse($collectiveinvoiceAttribute->toArray(), 'Collectiveinvoice Attribute retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCollectiveinvoiceAttributeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/collectiveinvoiceAttributes/{id}",
     *      summary="Update the specified CollectiveinvoiceAttribute in storage",
     *      tags={"CollectiveinvoiceAttribute"},
     *      description="Update CollectiveinvoiceAttribute",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CollectiveinvoiceAttribute",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CollectiveinvoiceAttribute that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CollectiveinvoiceAttribute")
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
     *                  ref="#/definitions/CollectiveinvoiceAttribute"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCollectiveinvoiceAttributeAPIRequest $request)
    {
        $input = $request->all();

        /** @var CollectiveinvoiceAttribute $collectiveinvoiceAttribute */
        $collectiveinvoiceAttribute = $this->collectiveinvoiceAttributeRepository->find($id);

        if (empty($collectiveinvoiceAttribute)) {
            return $this->sendError('Collectiveinvoice Attribute not found');
        }

        $collectiveinvoiceAttribute = $this->collectiveinvoiceAttributeRepository->update($input, $id);

        return $this->sendResponse($collectiveinvoiceAttribute->toArray(), 'CollectiveinvoiceAttribute updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/collectiveinvoiceAttributes/{id}",
     *      summary="Remove the specified CollectiveinvoiceAttribute from storage",
     *      tags={"CollectiveinvoiceAttribute"},
     *      description="Delete CollectiveinvoiceAttribute",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CollectiveinvoiceAttribute",
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
        /** @var CollectiveinvoiceAttribute $collectiveinvoiceAttribute */
        $collectiveinvoiceAttribute = $this->collectiveinvoiceAttributeRepository->find($id);

        if (empty($collectiveinvoiceAttribute)) {
            return $this->sendError('Collectiveinvoice Attribute not found');
        }

        $collectiveinvoiceAttribute->delete();

        return $this->sendResponse($id, 'Collectiveinvoice Attribute deleted successfully');
    }
}
