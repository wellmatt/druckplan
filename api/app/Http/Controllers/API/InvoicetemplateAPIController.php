<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateInvoicetemplateAPIRequest;
use App\Http\Requests\API\UpdateInvoicetemplateAPIRequest;
use App\Models\Invoicetemplate;
use App\Repositories\InvoicetemplateRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class InvoicetemplateController
 * @package App\Http\Controllers\API
 */

class InvoicetemplateAPIController extends AppBaseController
{
    /** @var  InvoicetemplateRepository */
    private $invoicetemplateRepository;

    public function __construct(InvoicetemplateRepository $invoicetemplateRepo)
    {
        $this->invoicetemplateRepository = $invoicetemplateRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/invoicetemplates",
     *      summary="Get a listing of the Invoicetemplates.",
     *      tags={"Invoicetemplate"},
     *      description="Get all Invoicetemplates",
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
     *                  @SWG\Items(ref="#/definitions/Invoicetemplate")
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
        $this->invoicetemplateRepository->pushCriteria(new RequestCriteria($request));
        $this->invoicetemplateRepository->pushCriteria(new LimitOffsetCriteria($request));
        $invoicetemplates = $this->invoicetemplateRepository->all();

        return $this->sendResponse($invoicetemplates->toArray(), 'Invoicetemplates retrieved successfully');
    }

    /**
     * @param CreateInvoicetemplateAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/invoicetemplates",
     *      summary="Store a newly created Invoicetemplate in storage",
     *      tags={"Invoicetemplate"},
     *      description="Store Invoicetemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Invoicetemplate that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Invoicetemplate")
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
     *                  ref="#/definitions/Invoicetemplate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateInvoicetemplateAPIRequest $request)
    {
        $input = $request->all();

        $invoicetemplates = $this->invoicetemplateRepository->create($input);

        return $this->sendResponse($invoicetemplates->toArray(), 'Invoicetemplate saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/invoicetemplates/{id}",
     *      summary="Display the specified Invoicetemplate",
     *      tags={"Invoicetemplate"},
     *      description="Get Invoicetemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Invoicetemplate",
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
     *                  ref="#/definitions/Invoicetemplate"
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
        /** @var Invoicetemplate $invoicetemplate */
        $invoicetemplate = $this->invoicetemplateRepository->find($id);

        if (empty($invoicetemplate)) {
            return $this->sendError('Invoicetemplate not found');
        }

        return $this->sendResponse($invoicetemplate->toArray(), 'Invoicetemplate retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateInvoicetemplateAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/invoicetemplates/{id}",
     *      summary="Update the specified Invoicetemplate in storage",
     *      tags={"Invoicetemplate"},
     *      description="Update Invoicetemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Invoicetemplate",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Invoicetemplate that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Invoicetemplate")
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
     *                  ref="#/definitions/Invoicetemplate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateInvoicetemplateAPIRequest $request)
    {
        $input = $request->all();

        /** @var Invoicetemplate $invoicetemplate */
        $invoicetemplate = $this->invoicetemplateRepository->find($id);

        if (empty($invoicetemplate)) {
            return $this->sendError('Invoicetemplate not found');
        }

        $invoicetemplate = $this->invoicetemplateRepository->update($input, $id);

        return $this->sendResponse($invoicetemplate->toArray(), 'Invoicetemplate updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/invoicetemplates/{id}",
     *      summary="Remove the specified Invoicetemplate from storage",
     *      tags={"Invoicetemplate"},
     *      description="Delete Invoicetemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Invoicetemplate",
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
        /** @var Invoicetemplate $invoicetemplate */
        $invoicetemplate = $this->invoicetemplateRepository->find($id);

        if (empty($invoicetemplate)) {
            return $this->sendError('Invoicetemplate not found');
        }

        $invoicetemplate->delete();

        return $this->sendResponse($id, 'Invoicetemplate deleted successfully');
    }
}
