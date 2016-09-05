<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaperSupplierAPIRequest;
use App\Http\Requests\API\UpdatePaperSupplierAPIRequest;
use App\Models\PaperSupplier;
use App\Repositories\PaperSupplierRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaperSupplierController
 * @package App\Http\Controllers\API
 */

class PaperSupplierAPIController extends AppBaseController
{
    /** @var  PaperSupplierRepository */
    private $paperSupplierRepository;

    public function __construct(PaperSupplierRepository $paperSupplierRepo)
    {
        $this->paperSupplierRepository = $paperSupplierRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paperSuppliers",
     *      summary="Get a listing of the PaperSuppliers.",
     *      tags={"PaperSupplier"},
     *      description="Get all PaperSuppliers",
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
     *                  @SWG\Items(ref="#/definitions/PaperSupplier")
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
        $this->paperSupplierRepository->pushCriteria(new RequestCriteria($request));
        $this->paperSupplierRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paperSuppliers = $this->paperSupplierRepository->all();

        return $this->sendResponse($paperSuppliers->toArray(), 'Paper Suppliers retrieved successfully');
    }

    /**
     * @param CreatePaperSupplierAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paperSuppliers",
     *      summary="Store a newly created PaperSupplier in storage",
     *      tags={"PaperSupplier"},
     *      description="Store PaperSupplier",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaperSupplier that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaperSupplier")
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
     *                  ref="#/definitions/PaperSupplier"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaperSupplierAPIRequest $request)
    {
        $input = $request->all();

        $paperSuppliers = $this->paperSupplierRepository->create($input);

        return $this->sendResponse($paperSuppliers->toArray(), 'Paper Supplier saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/paperSuppliers/{id}",
     *      summary="Display the specified PaperSupplier",
     *      tags={"PaperSupplier"},
     *      description="Get PaperSupplier",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaperSupplier",
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
     *                  ref="#/definitions/PaperSupplier"
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
        /** @var PaperSupplier $paperSupplier */
        $paperSupplier = $this->paperSupplierRepository->find($id);

        if (empty($paperSupplier)) {
            return $this->sendError('Paper Supplier not found');
        }

        return $this->sendResponse($paperSupplier->toArray(), 'Paper Supplier retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePaperSupplierAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paperSuppliers/{id}",
     *      summary="Update the specified PaperSupplier in storage",
     *      tags={"PaperSupplier"},
     *      description="Update PaperSupplier",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaperSupplier",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaperSupplier that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaperSupplier")
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
     *                  ref="#/definitions/PaperSupplier"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaperSupplierAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaperSupplier $paperSupplier */
        $paperSupplier = $this->paperSupplierRepository->find($id);

        if (empty($paperSupplier)) {
            return $this->sendError('Paper Supplier not found');
        }

        $paperSupplier = $this->paperSupplierRepository->update($input, $id);

        return $this->sendResponse($paperSupplier->toArray(), 'PaperSupplier updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paperSuppliers/{id}",
     *      summary="Remove the specified PaperSupplier from storage",
     *      tags={"PaperSupplier"},
     *      description="Delete PaperSupplier",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaperSupplier",
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
        /** @var PaperSupplier $paperSupplier */
        $paperSupplier = $this->paperSupplierRepository->find($id);

        if (empty($paperSupplier)) {
            return $this->sendError('Paper Supplier not found');
        }

        $paperSupplier->delete();

        return $this->sendResponse($id, 'Paper Supplier deleted successfully');
    }
}
