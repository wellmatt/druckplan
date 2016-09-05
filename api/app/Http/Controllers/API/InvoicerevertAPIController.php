<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateInvoicerevertAPIRequest;
use App\Http\Requests\API\UpdateInvoicerevertAPIRequest;
use App\Models\Invoicerevert;
use App\Repositories\InvoicerevertRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class InvoicerevertController
 * @package App\Http\Controllers\API
 */

class InvoicerevertAPIController extends AppBaseController
{
    /** @var  InvoicerevertRepository */
    private $invoicerevertRepository;

    public function __construct(InvoicerevertRepository $invoicerevertRepo)
    {
        $this->invoicerevertRepository = $invoicerevertRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/invoicereverts",
     *      summary="Get a listing of the Invoicereverts.",
     *      tags={"Invoicerevert"},
     *      description="Get all Invoicereverts",
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
     *                  @SWG\Items(ref="#/definitions/Invoicerevert")
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
        $this->invoicerevertRepository->pushCriteria(new RequestCriteria($request));
        $this->invoicerevertRepository->pushCriteria(new LimitOffsetCriteria($request));
        $invoicereverts = $this->invoicerevertRepository->all();

        return $this->sendResponse($invoicereverts->toArray(), 'Invoicereverts retrieved successfully');
    }

    /**
     * @param CreateInvoicerevertAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/invoicereverts",
     *      summary="Store a newly created Invoicerevert in storage",
     *      tags={"Invoicerevert"},
     *      description="Store Invoicerevert",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Invoicerevert that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Invoicerevert")
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
     *                  ref="#/definitions/Invoicerevert"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateInvoicerevertAPIRequest $request)
    {
        $input = $request->all();

        $invoicereverts = $this->invoicerevertRepository->create($input);

        return $this->sendResponse($invoicereverts->toArray(), 'Invoicerevert saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/invoicereverts/{id}",
     *      summary="Display the specified Invoicerevert",
     *      tags={"Invoicerevert"},
     *      description="Get Invoicerevert",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Invoicerevert",
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
     *                  ref="#/definitions/Invoicerevert"
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
        /** @var Invoicerevert $invoicerevert */
        $invoicerevert = $this->invoicerevertRepository->find($id);

        if (empty($invoicerevert)) {
            return $this->sendError('Invoicerevert not found');
        }

        return $this->sendResponse($invoicerevert->toArray(), 'Invoicerevert retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateInvoicerevertAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/invoicereverts/{id}",
     *      summary="Update the specified Invoicerevert in storage",
     *      tags={"Invoicerevert"},
     *      description="Update Invoicerevert",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Invoicerevert",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Invoicerevert that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Invoicerevert")
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
     *                  ref="#/definitions/Invoicerevert"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateInvoicerevertAPIRequest $request)
    {
        $input = $request->all();

        /** @var Invoicerevert $invoicerevert */
        $invoicerevert = $this->invoicerevertRepository->find($id);

        if (empty($invoicerevert)) {
            return $this->sendError('Invoicerevert not found');
        }

        $invoicerevert = $this->invoicerevertRepository->update($input, $id);

        return $this->sendResponse($invoicerevert->toArray(), 'Invoicerevert updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/invoicereverts/{id}",
     *      summary="Remove the specified Invoicerevert from storage",
     *      tags={"Invoicerevert"},
     *      description="Delete Invoicerevert",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Invoicerevert",
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
        /** @var Invoicerevert $invoicerevert */
        $invoicerevert = $this->invoicerevertRepository->find($id);

        if (empty($invoicerevert)) {
            return $this->sendError('Invoicerevert not found');
        }

        $invoicerevert->delete();

        return $this->sendResponse($id, 'Invoicerevert deleted successfully');
    }
}
