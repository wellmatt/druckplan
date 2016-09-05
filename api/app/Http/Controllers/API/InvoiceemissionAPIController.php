<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateInvoiceemissionAPIRequest;
use App\Http\Requests\API\UpdateInvoiceemissionAPIRequest;
use App\Models\Invoiceemission;
use App\Repositories\InvoiceemissionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class InvoiceemissionController
 * @package App\Http\Controllers\API
 */

class InvoiceemissionAPIController extends AppBaseController
{
    /** @var  InvoiceemissionRepository */
    private $invoiceemissionRepository;

    public function __construct(InvoiceemissionRepository $invoiceemissionRepo)
    {
        $this->invoiceemissionRepository = $invoiceemissionRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/invoiceemissions",
     *      summary="Get a listing of the Invoiceemissions.",
     *      tags={"Invoiceemission"},
     *      description="Get all Invoiceemissions",
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
     *                  @SWG\Items(ref="#/definitions/Invoiceemission")
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
        $this->invoiceemissionRepository->pushCriteria(new RequestCriteria($request));
        $this->invoiceemissionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $invoiceemissions = $this->invoiceemissionRepository->all();

        return $this->sendResponse($invoiceemissions->toArray(), 'Invoiceemissions retrieved successfully');
    }

    /**
     * @param CreateInvoiceemissionAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/invoiceemissions",
     *      summary="Store a newly created Invoiceemission in storage",
     *      tags={"Invoiceemission"},
     *      description="Store Invoiceemission",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Invoiceemission that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Invoiceemission")
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
     *                  ref="#/definitions/Invoiceemission"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateInvoiceemissionAPIRequest $request)
    {
        $input = $request->all();

        $invoiceemissions = $this->invoiceemissionRepository->create($input);

        return $this->sendResponse($invoiceemissions->toArray(), 'Invoiceemission saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/invoiceemissions/{id}",
     *      summary="Display the specified Invoiceemission",
     *      tags={"Invoiceemission"},
     *      description="Get Invoiceemission",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Invoiceemission",
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
     *                  ref="#/definitions/Invoiceemission"
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
        /** @var Invoiceemission $invoiceemission */
        $invoiceemission = $this->invoiceemissionRepository->find($id);

        if (empty($invoiceemission)) {
            return $this->sendError('Invoiceemission not found');
        }

        return $this->sendResponse($invoiceemission->toArray(), 'Invoiceemission retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateInvoiceemissionAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/invoiceemissions/{id}",
     *      summary="Update the specified Invoiceemission in storage",
     *      tags={"Invoiceemission"},
     *      description="Update Invoiceemission",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Invoiceemission",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Invoiceemission that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Invoiceemission")
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
     *                  ref="#/definitions/Invoiceemission"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateInvoiceemissionAPIRequest $request)
    {
        $input = $request->all();

        /** @var Invoiceemission $invoiceemission */
        $invoiceemission = $this->invoiceemissionRepository->find($id);

        if (empty($invoiceemission)) {
            return $this->sendError('Invoiceemission not found');
        }

        $invoiceemission = $this->invoiceemissionRepository->update($input, $id);

        return $this->sendResponse($invoiceemission->toArray(), 'Invoiceemission updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/invoiceemissions/{id}",
     *      summary="Remove the specified Invoiceemission from storage",
     *      tags={"Invoiceemission"},
     *      description="Delete Invoiceemission",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Invoiceemission",
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
        /** @var Invoiceemission $invoiceemission */
        $invoiceemission = $this->invoiceemissionRepository->find($id);

        if (empty($invoiceemission)) {
            return $this->sendError('Invoiceemission not found');
        }

        $invoiceemission->delete();

        return $this->sendResponse($id, 'Invoiceemission deleted successfully');
    }
}
