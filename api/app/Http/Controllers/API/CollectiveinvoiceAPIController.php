<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCollectiveinvoiceAPIRequest;
use App\Http\Requests\API\UpdateCollectiveinvoiceAPIRequest;
use App\Models\Collectiveinvoice;
use App\Repositories\CollectiveinvoiceRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CollectiveinvoiceController
 * @package App\Http\Controllers\API
 */

class CollectiveinvoiceAPIController extends AppBaseController
{
    /** @var  CollectiveinvoiceRepository */
    private $collectiveinvoiceRepository;

    public function __construct(CollectiveinvoiceRepository $collectiveinvoiceRepo)
    {
        $this->collectiveinvoiceRepository = $collectiveinvoiceRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/collectiveinvoices",
     *      summary="Get a listing of the Collectiveinvoices.",
     *      tags={"Collectiveinvoice"},
     *      description="Get all Collectiveinvoices",
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
     *                  @SWG\Items(ref="#/definitions/Collectiveinvoice")
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
        $this->collectiveinvoiceRepository->pushCriteria(new RequestCriteria($request));
        $this->collectiveinvoiceRepository->pushCriteria(new LimitOffsetCriteria($request));
        $collectiveinvoices = $this->collectiveinvoiceRepository->all();

        return $this->sendResponse($collectiveinvoices->toArray(), 'Collectiveinvoices retrieved successfully');
    }

    /**
     * @param CreateCollectiveinvoiceAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/collectiveinvoices",
     *      summary="Store a newly created Collectiveinvoice in storage",
     *      tags={"Collectiveinvoice"},
     *      description="Store Collectiveinvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Collectiveinvoice that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Collectiveinvoice")
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
     *                  ref="#/definitions/Collectiveinvoice"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCollectiveinvoiceAPIRequest $request)
    {
        $input = $request->all();

        $collectiveinvoices = $this->collectiveinvoiceRepository->create($input);

        return $this->sendResponse($collectiveinvoices->toArray(), 'Collectiveinvoice saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/collectiveinvoices/{id}",
     *      summary="Display the specified Collectiveinvoice",
     *      tags={"Collectiveinvoice"},
     *      description="Get Collectiveinvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Collectiveinvoice",
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
     *                  ref="#/definitions/Collectiveinvoice"
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
        /** @var Collectiveinvoice $collectiveinvoice */
        $collectiveinvoice = $this->collectiveinvoiceRepository->find($id);

        if (empty($collectiveinvoice)) {
            return $this->sendError('Collectiveinvoice not found');
        }

        return $this->sendResponse($collectiveinvoice->toArray(), 'Collectiveinvoice retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCollectiveinvoiceAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/collectiveinvoices/{id}",
     *      summary="Update the specified Collectiveinvoice in storage",
     *      tags={"Collectiveinvoice"},
     *      description="Update Collectiveinvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Collectiveinvoice",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Collectiveinvoice that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Collectiveinvoice")
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
     *                  ref="#/definitions/Collectiveinvoice"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCollectiveinvoiceAPIRequest $request)
    {
        $input = $request->all();

        /** @var Collectiveinvoice $collectiveinvoice */
        $collectiveinvoice = $this->collectiveinvoiceRepository->find($id);

        if (empty($collectiveinvoice)) {
            return $this->sendError('Collectiveinvoice not found');
        }

        $collectiveinvoice = $this->collectiveinvoiceRepository->update($input, $id);

        return $this->sendResponse($collectiveinvoice->toArray(), 'Collectiveinvoice updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/collectiveinvoices/{id}",
     *      summary="Remove the specified Collectiveinvoice from storage",
     *      tags={"Collectiveinvoice"},
     *      description="Delete Collectiveinvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Collectiveinvoice",
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
        /** @var Collectiveinvoice $collectiveinvoice */
        $collectiveinvoice = $this->collectiveinvoiceRepository->find($id);

        if (empty($collectiveinvoice)) {
            return $this->sendError('Collectiveinvoice not found');
        }

        $collectiveinvoice->delete();

        return $this->sendResponse($id, 'Collectiveinvoice deleted successfully');
    }
}
