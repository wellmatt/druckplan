<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCollectiveinvoiceOrderpositionAPIRequest;
use App\Http\Requests\API\UpdateCollectiveinvoiceOrderpositionAPIRequest;
use App\Models\CollectiveinvoiceOrderposition;
use App\Repositories\CollectiveinvoiceOrderpositionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CollectiveinvoiceOrderpositionController
 * @package App\Http\Controllers\API
 */

class CollectiveinvoiceOrderpositionAPIController extends AppBaseController
{
    /** @var  CollectiveinvoiceOrderpositionRepository */
    private $collectiveinvoiceOrderpositionRepository;

    public function __construct(CollectiveinvoiceOrderpositionRepository $collectiveinvoiceOrderpositionRepo)
    {
        $this->collectiveinvoiceOrderpositionRepository = $collectiveinvoiceOrderpositionRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/collectiveinvoiceOrderpositions",
     *      summary="Get a listing of the CollectiveinvoiceOrderpositions.",
     *      tags={"CollectiveinvoiceOrderposition"},
     *      description="Get all CollectiveinvoiceOrderpositions",
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
     *                  @SWG\Items(ref="#/definitions/CollectiveinvoiceOrderposition")
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
        $this->collectiveinvoiceOrderpositionRepository->pushCriteria(new RequestCriteria($request));
        $this->collectiveinvoiceOrderpositionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $collectiveinvoiceOrderpositions = $this->collectiveinvoiceOrderpositionRepository->all();

        return $this->sendResponse($collectiveinvoiceOrderpositions->toArray(), 'Collectiveinvoice Orderpositions retrieved successfully');
    }

    /**
     * @param CreateCollectiveinvoiceOrderpositionAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/collectiveinvoiceOrderpositions",
     *      summary="Store a newly created CollectiveinvoiceOrderposition in storage",
     *      tags={"CollectiveinvoiceOrderposition"},
     *      description="Store CollectiveinvoiceOrderposition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CollectiveinvoiceOrderposition that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CollectiveinvoiceOrderposition")
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
     *                  ref="#/definitions/CollectiveinvoiceOrderposition"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCollectiveinvoiceOrderpositionAPIRequest $request)
    {
        $input = $request->all();

        $collectiveinvoiceOrderpositions = $this->collectiveinvoiceOrderpositionRepository->create($input);

        return $this->sendResponse($collectiveinvoiceOrderpositions->toArray(), 'Collectiveinvoice Orderposition saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/collectiveinvoiceOrderpositions/{id}",
     *      summary="Display the specified CollectiveinvoiceOrderposition",
     *      tags={"CollectiveinvoiceOrderposition"},
     *      description="Get CollectiveinvoiceOrderposition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CollectiveinvoiceOrderposition",
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
     *                  ref="#/definitions/CollectiveinvoiceOrderposition"
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
        /** @var CollectiveinvoiceOrderposition $collectiveinvoiceOrderposition */
        $collectiveinvoiceOrderposition = $this->collectiveinvoiceOrderpositionRepository->find($id);

        if (empty($collectiveinvoiceOrderposition)) {
            return $this->sendError('Collectiveinvoice Orderposition not found');
        }

        return $this->sendResponse($collectiveinvoiceOrderposition->toArray(), 'Collectiveinvoice Orderposition retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCollectiveinvoiceOrderpositionAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/collectiveinvoiceOrderpositions/{id}",
     *      summary="Update the specified CollectiveinvoiceOrderposition in storage",
     *      tags={"CollectiveinvoiceOrderposition"},
     *      description="Update CollectiveinvoiceOrderposition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CollectiveinvoiceOrderposition",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CollectiveinvoiceOrderposition that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CollectiveinvoiceOrderposition")
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
     *                  ref="#/definitions/CollectiveinvoiceOrderposition"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCollectiveinvoiceOrderpositionAPIRequest $request)
    {
        $input = $request->all();

        /** @var CollectiveinvoiceOrderposition $collectiveinvoiceOrderposition */
        $collectiveinvoiceOrderposition = $this->collectiveinvoiceOrderpositionRepository->find($id);

        if (empty($collectiveinvoiceOrderposition)) {
            return $this->sendError('Collectiveinvoice Orderposition not found');
        }

        $collectiveinvoiceOrderposition = $this->collectiveinvoiceOrderpositionRepository->update($input, $id);

        return $this->sendResponse($collectiveinvoiceOrderposition->toArray(), 'CollectiveinvoiceOrderposition updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/collectiveinvoiceOrderpositions/{id}",
     *      summary="Remove the specified CollectiveinvoiceOrderposition from storage",
     *      tags={"CollectiveinvoiceOrderposition"},
     *      description="Delete CollectiveinvoiceOrderposition",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CollectiveinvoiceOrderposition",
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
        /** @var CollectiveinvoiceOrderposition $collectiveinvoiceOrderposition */
        $collectiveinvoiceOrderposition = $this->collectiveinvoiceOrderpositionRepository->find($id);

        if (empty($collectiveinvoiceOrderposition)) {
            return $this->sendError('Collectiveinvoice Orderposition not found');
        }

        $collectiveinvoiceOrderposition->delete();

        return $this->sendResponse($id, 'Collectiveinvoice Orderposition deleted successfully');
    }
}
